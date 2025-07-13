<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartSummary = $this->calculateCartSummary($cartItems);
        
        return view('frontend.cart.index', compact('cartItems', 'cartSummary'));
    }

    // FIXED: Method add dengan parameter yang jelas dan error handling yang lebih baik
    public function add(Request $request, $productId)
    {
        // Log untuk debugging
        Log::info('Cart add request', [
            'product_id' => $productId,
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to cart',
                'redirect' => route('login')
            ], 401);
        }

        // Find product
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock_quantity
        ]);

        $quantity = $request->quantity;

        // Check if product is active and in stock
        if (!$product->is_active || $product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available or insufficient stock'
            ]);
        }

        try {
            // For authenticated users, use database
            $cartItem = ShoppingCart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Update existing cart item
                $newQuantity = $cartItem->quantity + $quantity;
                
                if ($newQuantity > $product->stock_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add more items. Stock limit reached.'
                    ]);
                }
                
                $cartItem->update(['quantity' => $newQuantity]);
                Log::info('Cart item updated', ['cart_item_id' => $cartItem->id, 'new_quantity' => $newQuantity]);
            } else {
                // Create new cart item
                $cartItem = ShoppingCart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price
                ]);
                Log::info('New cart item created', ['cart_item_id' => $cartItem->id]);
            }

            $cartCount = $this->getCartCount();
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => $cartCount,
                'product_name' => $product->name
            ]);

        } catch (\Exception $e) {
            Log::error('Cart add error', [
                'error' => $e->getMessage(),
                'product_id' => $productId,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error adding product to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $cartItemId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to update cart',
                'redirect' => route('login')
            ], 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $quantity = $request->quantity;

        try {
            $cartItem = ShoppingCart::where('user_id', Auth::id())
                ->where('id', $cartItemId)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ]);
            }

            $product = $cartItem->product;

            if ($quantity > $product->stock_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available'
                ]);
            }

            $cartItem->update(['quantity' => $quantity]);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_count' => $this->getCartCount()
            ]);

        } catch (\Exception $e) {
            Log::error('Cart update error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error updating cart'
            ]);
        }
    }

    public function remove($cartItemId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to remove items from cart',
                'redirect' => route('login')
            ], 401);
        }

        try {
            $cartItem = ShoppingCart::where('user_id', Auth::id())
                ->where('id', $cartItemId)
                ->first();

            if ($cartItem) {
                $cartItem->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $this->getCartCount()
            ]);

        } catch (\Exception $e) {
            Log::error('Cart remove error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error removing item from cart'
            ]);
        }
    }

    public function clear()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to clear cart',
                'redirect' => route('login')
            ], 401);
        }

        try {
            ShoppingCart::where('user_id', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'cart_count' => 0
            ]);

        } catch (\Exception $e) {
            Log::error('Cart clear error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cart'
            ]);
        }
    }

    public function count()
    {
        return response()->json([
            'count' => $this->getCartCount()
        ]);
    }

    private function getCartItems()
    {
        $cartItems = collect();
        
        if (Auth::check()) {
            $cartItems = ShoppingCart::with('product.images')
                ->where('user_id', Auth::id())
                ->get();
        }
        
        return $cartItems;
    }

    private function getCartCount()
    {
        if (Auth::check()) {
            return ShoppingCart::where('user_id', Auth::id())->sum('quantity');
        }
        
        return 0;
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
            'subtotal' => $subtotal,
            'subtotal_formatted' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'shipping' => $shipping,
            'shipping_formatted' => $shipping > 0 ? 'Rp ' . number_format($shipping, 0, ',', '.') : 'FREE',
            'tax' => $tax,
            'tax_formatted' => 'Rp ' . number_format($tax, 0, ',', '.'),
            'total' => $total,
            'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.'),
        ];
    }
}