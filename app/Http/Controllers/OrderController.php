<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $query = Order::with(['items.product.images'])
            ->where('user_id', Auth::id());

        // ENHANCED: Apply filters
        $this->applyFilters($query, $request);

        // Apply sorting
        $this->applySorting($query, $request);

        // Paginate results
        $orders = $query->paginate(10);

        return view('frontend.orders.index', compact('orders'));
    }

    private function applyFilters($query, Request $request)
    {
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_range')) {
            $dateRange = $request->date_range;
            $startDate = null;
            
            switch ($dateRange) {
                case '7days':
                    $startDate = Carbon::now()->subDays(7);
                    break;
                case '30days':
                    $startDate = Carbon::now()->subDays(30);
                    break;
                case '90days':
                    $startDate = Carbon::now()->subDays(90);
                    break;
                case '1year':
                    $startDate = Carbon::now()->subYear();
                    break;
            }
            
            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Amount range filter
        if ($request->filled('amount_range')) {
            $range = explode('-', $request->amount_range);
            if (count($range) === 2) {
                $min = (float) $range[0];
                $max = (float) $range[1];
                
                $query->whereBetween('total_amount', [$min, $max]);
            }
        }
    }

    private function applySorting($query, Request $request)
    {
        $sort = $request->get('sort', 'newest');
        
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest_amount':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'lowest_amount':
                $query->orderBy('total_amount', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
    }

    public function show(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        $order->load(['items.product.images']);

        return view('frontend.orders.show', compact('order'));
    }

    public function track(Order $order)
    {
        // Ensure user can only track their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        $order->load(['items.product.images']);

        return view('frontend.orders.track', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to order.'
            ], 403);
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled at this stage.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->get('reason', 'Cancelled by customer')
            ]);

            // Restore product stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling order. Please try again.'
            ]);
        }
    }

    public function reorder(Request $request, Order $order)
    {
        // Ensure user can only reorder their own orders
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to order.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $addedItems = 0;
            $unavailableItems = [];

            foreach ($order->items as $item) {
                $product = $item->product;
                
                if (!$product || !$product->is_active) {
                    $unavailableItems[] = $item->product_name;
                    continue;
                }

                if ($product->stock_quantity < $item->quantity) {
                    $unavailableItems[] = $product->name . ' (insufficient stock)';
                    continue;
                }

                // Check if item already exists in cart
                $cartItem = ShoppingCart::where('user_id', Auth::id())
                    ->where('product_id', $product->id)
                    ->first();

                if ($cartItem) {
                    // Update existing cart item
                    $newQuantity = $cartItem->quantity + $item->quantity;
                    if ($newQuantity <= $product->stock_quantity) {
                        $cartItem->update(['quantity' => $newQuantity]);
                        $addedItems++;
                    } else {
                        $unavailableItems[] = $product->name . ' (would exceed stock limit)';
                    }
                } else {
                    // Create new cart item
                    ShoppingCart::create([
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'price' => $product->price
                    ]);
                    $addedItems++;
                }
            }

            DB::commit();

            $message = $addedItems > 0 ? 
                "{$addedItems} items added to cart." : 
                "No items could be added to cart.";

            if (!empty($unavailableItems)) {
                $message .= " Some items were unavailable: " . implode(', ', $unavailableItems);
            }

            return response()->json([
                'success' => $addedItems > 0,
                'message' => $message,
                'added_items' => $addedItems,
                'unavailable_items' => $unavailableItems
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing reorder. Please try again.'
            ]);
        }
    }

    public function invoice(Order $order)
    {
        // Ensure user can only download their own invoices
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Only delivered orders can have invoices
        if ($order->status !== 'delivered') {
            abort(404, 'Invoice not available for this order.');
        }

        $order->load(['items.product']);

        return view('frontend.orders.invoice', compact('order'));
    }

    public function uploadPayment(Request $request, Order $order)
{
    // Log untuk debugging
    \Log::info('Payment upload attempt', [
        'order_id' => $order->id,
        'user_id' => auth()->id()
    ]);

    // Ensure user can only upload payment for their own orders
    if ($order->user_id !== Auth::id()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access to order.'
        ], 403);
    }

    // UPDATED: More flexible validation
    if ($order->payment_method !== 'bank_transfer') {
        return response()->json([
            'success' => false,
            'message' => 'Payment upload is only available for bank transfer orders.'
        ], 400);
    }

    if (!in_array($order->payment_status, ['pending'])) {
        return response()->json([
            'success' => false,
            'message' => 'Payment proof cannot be uploaded for this order status.'
        ], 400);
    }

    // Validate file
    try {
        $request->validate([
            'payment_proof' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:2048'
            ]
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid file. Please upload a valid image (JPG, PNG, GIF) max 2MB.',
            'errors' => $e->errors()
        ], 422);
    }

    try {
        // Delete old payment proof if exists
        if ($order->payment_proof && \Storage::disk('public')->exists($order->payment_proof)) {
            \Storage::disk('public')->delete($order->payment_proof);
        }

        // Store the new payment proof
        $file = $request->file('payment_proof');
        $filename = 'payment_proof_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('payment-proofs', $filename, 'public');

        // SOLUSI: TETAP PAKAI 'pending' + notes untuk tracking
        $updateData = [
            'payment_proof' => $path,
            'payment_status' => 'pending', // ← GANTI DARI 'pending_verification' KE 'pending'
            'payment_proof_uploaded_at' => now()
        ];

        // Tambah notes untuk admin tahu kalau payment proof sudah diupload
        $newNote = "[" . now()->format('d M Y H:i') . "] Payment proof uploaded by customer - awaiting admin verification";
        if ($order->notes) {
            $updateData['notes'] = $order->notes . "\n" . $newNote;
        } else {
            $updateData['notes'] = $newNote;
        }

        $order->update($updateData);

        \Log::info('Payment proof uploaded successfully', [
            'order_id' => $order->id,
            'file_path' => $path,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment proof uploaded successfully. We will verify your payment within 24 hours.',
            'order_status' => $order->status,
            'payment_status' => $order->payment_status,
            'has_payment_proof' => true
        ]);

    } catch (\Exception $e) {
        \Log::error('Payment proof upload failed', [
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error uploading payment proof: ' . $e->getMessage()
        ], 500);
    }
}

    public function paymentStatus(Order $order)
    {
        // Ensure user can only check their own order status
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to order.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'payment_status' => $order->payment_status,
            'order_status' => $order->status,
            'last_updated' => $order->updated_at->toISOString()
        ]);
    }
}