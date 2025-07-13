<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index()
    {
        try {
            // Get cart items
            $cartItems = $this->getCartItems();
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')
                    ->with('error', 'Your cart is empty');
            }
    
            // Calculate totals
            $cartSummary = $this->calculateCartSummary($cartItems);
            
            // Get user dengan data default yang aman
            $user = Auth::user();
            
            // Pastikan user memiliki data minimal
            if (!$user) {
                return redirect()->route('login')
                    ->with('message', 'Please login to continue with checkout.');
            }
            
            // Buat object user dengan data default jika null
            $userData = (object) [
                'id' => $user->id,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
                'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
            ];
            
            // Get user addresses (kosong untuk sementara)
            $savedAddresses = [];
    
            return view('frontend.checkout.index', [
                'cartItems' => $cartItems,
                'cartSummary' => $cartSummary,
                'user' => $userData,
                'savedAddresses' => $savedAddresses
            ]);
            
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Checkout Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            
            return redirect()->route('cart.index')
                ->with('error', 'Something went wrong. Please try again. Error: ' . $e->getMessage());
        }
    }


// GANTI method process() di CheckoutController dengan versi sederhana:

public function process(Request $request)
{
    $request->validate([
        'shipping_first_name' => 'required|string|max:255',
        'shipping_last_name' => 'required|string|max:255',
        'shipping_email' => 'required|email|max:255',
        'shipping_phone' => 'required|string|max:20',
        'shipping_address' => 'required|string|max:500',
        'shipping_city' => 'required|string|max:100',
        'shipping_state' => 'required|string|max:100',
        'shipping_zip' => 'required|string|max:20',
        'shipping_country' => 'required|string|max:100',
        'notes' => 'nullable|string|max:1000',
    ]);

    try {
        DB::beginTransaction();

        // Get cart items
        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        // Calculate totals
        $cartSummary = $this->calculateCartSummaryRaw($cartItems);

        // Create order - SELALU bank transfer
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => Order::generateOrderNumber(),
            'status' => Order::STATUS_PENDING,
            
            'total_amount' => $cartSummary['total'],
            'shipping_cost' => $cartSummary['shipping'],
            'tax_amount' => $cartSummary['tax'],
            'discount_amount' => $cartSummary['discount'] ?? 0,
            
            'customer_name' => $request->shipping_first_name . ' ' . $request->shipping_last_name,
            'customer_email' => $request->shipping_email,
            'customer_phone' => $request->shipping_phone,
            
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_postal_code' => $request->shipping_zip,
            'shipping_province' => $request->shipping_state,
            
            'payment_method' => 'bank_transfer', // SELALU bank transfer
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            
            'notes' => $request->notes,
        ]);

        // Create order items
        foreach ($cartItems as $item) {
            $product = $item->product ?? Product::find($item->product_id);
            
            if (!$product) {
                throw new \Exception("Product not found for cart item");
            }
            
            if ($product->stock_quantity < $item->quantity) {
                throw new \Exception("Insufficient stock for {$product->name}");
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku ?? 'N/A',
                'quantity' => $item->quantity,
                'price' => $item->price ?? $product->price,
                'total' => ($item->price ?? $product->price) * $item->quantity,
            ]);

            // Decrease product stock
            $product->decrement('stock_quantity', $item->quantity);
        }

        // Clear cart
        $this->clearCart();

        DB::commit();

        // SELALU redirect ke bank transfer
        return redirect()->route('checkout.bank-transfer', $order)
            ->with('success', 'Order placed! Please complete payment via bank transfer.');

    } catch (\Exception $e) {
        DB::rollback();
        
        \Log::error('Checkout Error: ' . $e->getMessage());
        
        return redirect()->back()
            ->with('error', 'Failed to process order: ' . $e->getMessage())
            ->withInput();
    }
}

    public function success(Order $order)
    {
        // Ensure user can only see their own order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product', 'user']);

        return view('frontend.checkout.success', compact('order'));
    }

    public function failed()
    {
        return view('frontend.checkout.failed');
    }

    private function getCartItems()
    {
        $cartItems = collect();
        
        if (Auth::check()) {
            $cartItems = ShoppingCart::with('product.images')
                ->where('user_id', Auth::id())
                ->get();
        } else {
            $cart = Session::get('cart', []);
            foreach ($cart as $key => $item) {
                $product = Product::with('images')->find($item['product_id']);
                if ($product) {
                    $cartItems->push((object)[
                        'id' => $key,
                        'product' => $product,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
            }
        }
        
        return $cartItems;
    }

    private function calculateCartSummary($cartItems)
    {
        $subtotal = 0;
        
        foreach ($cartItems as $item) {
            $price = $item->price ?? $item->product->price;
            $subtotal += $item->quantity * $price;
        }
        
        $shipping = $subtotal > 1000000 ? 0 : 25000; // Free shipping over 1M IDR
        $tax = $subtotal * 0.11; // PPN 11%
        $total = $subtotal + $shipping + $tax;
        
        return [
            'subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'shipping' => $shipping > 0 ? 'Rp ' . number_format($shipping, 0, ',', '.') : 'FREE',
            'tax' => 'Rp ' . number_format($tax, 0, ',', '.'),
            'total' => 'Rp ' . number_format($total, 0, ',', '.'),
        ];
    }

    private function calculateCartSummaryRaw($cartItems)
    {
        $subtotal = 0;
        
        foreach ($cartItems as $item) {
            $price = $item->price ?? $item->product->price;
            $subtotal += $item->quantity * $price;
        }
        
        $shipping = $subtotal > 1000000 ? 0 : 25000;
        $tax = $subtotal * 0.11;
        $total = $subtotal + $shipping + $tax;
        
        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'discount' => 0, // Nanti bisa ditambah logic discount
        ];
    }

    private function clearCart()
    {
        if (Auth::check()) {
            ShoppingCart::where('user_id', Auth::id())->delete();
        } else {
            Session::forget('cart');
        }
    }

    public function bankTransfer(Order $order)
{
    // Pastikan user hanya bisa akses order sendiri
    if ($order->user_id !== Auth::id()) {
        abort(403);
    }

    // Pastikan order menggunakan bank transfer
    if ($order->payment_method !== 'bank_transfer') {
        return redirect()->route('checkout.success', $order);
    }

    return view('frontend.checkout.bank-transfer', compact('order'));
}

public function ewallet(Order $order)
{
    // Pastikan user hanya bisa akses order sendiri
    if ($order->user_id !== Auth::id()) {
        abort(403);
    }

    // Pastikan order menggunakan e-wallet
    if ($order->payment_method !== 'ewallet') {
        return redirect()->route('checkout.success', $order);
    }

    return view('frontend.checkout.ewallet', compact('order'));
}

public function uploadPaymentProof(Request $request, Order $order)
{
    // Pastikan user hanya bisa upload untuk order sendiri
    if ($order->user_id !== Auth::id()) {
        return response()->json([
            'success' => false, 
            'message' => 'Unauthorized access'
        ], 403);
    }

    // Validasi file
    try {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        $errors = $e->validator->errors()->all();
        return response()->json([
            'success' => false,
            'message' => 'Validation failed: ' . implode(', ', $errors)
        ], 422);
    }

    try {
        // Check if file was uploaded
        if (!$request->hasFile('payment_proof')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 422);
        }

        $file = $request->file('payment_proof');
        
        // Check if file is valid
        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Uploaded file is corrupted or invalid'
            ], 422);
        }

        // Create directory if it doesn't exist
        $uploadPath = storage_path('app/public/payment_proofs');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $filename = 'payment_proof_' . $order->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store file
        $path = $file->storeAs('payment_proofs', $filename, 'public');
        
        // Verify file was actually stored
        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store file on server'
            ], 500);
        }

        // Check if stored file exists
        if (!file_exists(storage_path('app/public/' . $path))) {
            return response()->json([
                'success' => false,
                'message' => 'File uploaded but not found on server'
            ], 500);
        }

        // Update order dengan status yang VALID sesuai migration
        $updateResult = $order->update([
            'payment_proof' => $path,
            'payment_status' => 'pending', // Tetap pending (sesuai enum di migration)
            'status' => 'confirmed'         // Update status order
        ]);

        if (!$updateResult) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order in database'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment proof uploaded successfully! We will verify your payment within 24 hours.',
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'file_name' => $filename
        ]);

    } catch (\Exception $e) {
        // Log detailed error
        \Log::error('Upload Payment Proof Error', [
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage(),
            'error_details' => config('app.debug') ? [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ] : null
        ], 500);
    }
}

    private function processPayment(Order $order, Request $request)
    {
        // Process payment based on method
        switch ($request->payment_method) {
            case 'credit_card':
                // Integrate with payment gateway (Midtrans, Xendit, etc.)
                return ['success' => true, 'message' => 'Payment processed'];
                
            case 'bank_transfer':
                // Generate bank transfer instructions
                $order->update([
                    'payment_status' => 'pending',
                    'payment_reference' => 'BT-' . strtoupper(uniqid()),
                ]);
                return ['success' => true, 'message' => 'Bank transfer instructions sent'];
                
            case 'cod':
                // Cash on delivery - no payment processing needed
                $order->update(['payment_status' => 'pending']);
                return ['success' => true, 'message' => 'Cash on delivery order placed'];
                
            case 'ewallet':
                // Integrate with e-wallet (GoPay, OVO, Dana, etc.)
                return ['success' => true, 'message' => 'E-wallet payment processed'];
                
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }
}