@extends('layouts.frontend')

@section('title', 'Shopping Cart - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Shopping Cart</li>
        </ol>
    </nav>

    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    @if($cartItems->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border">
                <div class="p-6 border-b">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold">Cart Items ({{ $cartItems->count() }})</h2>
                        <button onclick="clearCart()" class="text-red-500 hover:text-red-600 text-sm">
                            <i class="fas fa-trash mr-1"></i>Clear Cart
                        </button>
                    </div>
                </div>

                <div class="divide-y">
                    @foreach($cartItems as $item)
                    <div class="p-6" id="cart-item-{{ $item->id }}">
                        <div class="flex items-center space-x-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded overflow-hidden">
                                @if($item->product->images->count() > 0)
                                    <img src="{{ Storage::url($item->product->images->first()->image_path) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">
                                    <a href="{{ route('products.show', $item->product->slug) }}" 
                                       class="hover:text-red-500">{{ $item->product->name }}</a>
                                </h3>
                                <p class="text-sm text-gray-600 mb-2">SKU: {{ $item->product->sku }}</p>
                                
                                <!-- Stock Status -->
                                @if($item->product->stock_quantity >= $item->quantity)
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                        In Stock
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded">
                                        Low Stock ({{ $item->product->stock_quantity }} left)
                                    </span>
                                @endif
                            </div>

                            <!-- Price -->
                            <div class="text-center">
                                <p class="text-lg font-semibold text-gray-900">
                                    Rp {{ number_format($item->price ?? $item->product->price, 0, ',', '.') }}
                                </p>
                                <p class="text-sm text-gray-600">per item</p>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center border border-gray-300 rounded">
                                    <button type="button" 
                                            onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                            class="px-3 py-2 hover:bg-gray-100 transition-colors {{ $item->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                        -
                                    </button>
                                    <input type="number" 
                                           value="{{ $item->quantity }}" 
                                           min="1" 
                                           max="{{ $item->product->stock_quantity }}"
                                           class="w-16 px-3 py-2 text-center border-0 focus:ring-0"
                                           onchange="updateQuantity({{ $item->id }}, this.value)">
                                    <button type="button" 
                                            onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                            class="px-3 py-2 hover:bg-gray-100 transition-colors {{ $item->quantity >= $item->product->stock_quantity ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}>
                                        +
                                    </button>
                                </div>
                            </div>

                            <!-- Total Price -->
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900" id="item-total-{{ $item->id }}">
                                    Rp {{ number_format(($item->price ?? $item->product->price) * $item->quantity, 0, ',', '.') }}
                                </p>
                                <button onclick="removeItem({{ $item->id }})" 
                                        class="text-red-500 hover:text-red-600 text-sm mt-2">
                                    <i class="fas fa-trash mr-1"></i>Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Continue Shopping -->
            <div class="mt-6">
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center text-red-500 hover:text-red-600">
                    <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                </a>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal ({{ $cartItems->count() }} items)</span>
                        <span class="text-gray-900" id="cart-subtotal">{{ $cartSummary['subtotal_formatted'] }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-900" id="cart-shipping">{{ $cartSummary['shipping_formatted'] }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax (PPN 11%)</span>
                        <span class="text-gray-900" id="cart-tax">{{ $cartSummary['tax_formatted'] }}</span>
                    </div>
                    
                    <div class="border-t pt-4">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold text-gray-900">Total</span>
                            <span class="text-lg font-semibold text-gray-900" id="cart-total">{{ $cartSummary['total_formatted'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Checkout Button -->
                @auth
                    <a href="{{ route('checkout.index') }}" 
                       class="w-full bg-red-500 text-white py-3 px-6 rounded-lg hover:bg-red-600 transition-colors font-medium mt-6 block text-center">
                        Proceed to Checkout
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="w-full bg-red-500 text-white py-3 px-6 rounded-lg hover:bg-red-600 transition-colors font-medium mt-6 block text-center">
                        Login to Checkout
                    </a>
                @endauth

                <!-- Security Info -->
                <div class="mt-4 pt-4 border-t">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <i class="fas fa-lock"></i>
                        <span>Secure checkout guaranteed</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-600 mt-2">
                        <i class="fas fa-truck"></i>
                        <span>Free shipping on orders over Rp 1.000.000</span>
                    </div>
                </div>
{{-- 
                <!-- Promo Code -->
                <div class="mt-6 pt-4 border-t">
                    <h3 class="font-medium text-gray-900 mb-3">Promo Code</h3>
                    <div class="flex space-x-2">
                        <input type="text" placeholder="Enter promo code" 
                               class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-200 transition-colors">
                            Apply
                        </button>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

    @else
    <!-- Empty Cart -->
    <div class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-shopping-cart text-6xl text-gray-400 mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-600 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-6">Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ route('products.index') }}" 
               class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors inline-block">
                Start Shopping
            </a>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function updateQuantity(itemId, quantity) {
    if (quantity < 1) return;
    
    fetch(`/cart/update/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to update totals
            location.reload();
        } else {
            showNotification(data.message || 'Error updating quantity', 'error');
        }
    })
    .catch(error => {
        showNotification('Error updating quantity', 'error');
    });
}

function removeItem(itemId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    fetch(`/cart/remove/${itemId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove item from DOM
            document.getElementById(`cart-item-${itemId}`).remove();
            
            // Update cart count
            updateCartCount();
            
            // Reload page if no items left
            const remainingItems = document.querySelectorAll('[id^="cart-item-"]');
            if (remainingItems.length === 0) {
                location.reload();
            }
            
            showNotification('Item removed from cart', 'success');
        } else {
            showNotification(data.message || 'Error removing item', 'error');
        }
    })
    .catch(error => {
        showNotification('Error removing item', 'error');
    });
}

function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) {
        return;
    }
    
    fetch('/cart/clear', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message || 'Error clearing cart', 'error');
        }
    })
    .catch(error => {
        showNotification('Error clearing cart', 'error');
    });
}

function updateCartCount() {
    fetch('/cart/count')
    .then(response => response.json())
    .then(data => {
        const cartBadge = document.querySelector('.cart-count');
        if (cartBadge) {
            cartBadge.textContent = data.count;
        }
    })
    .catch(error => console.log('Error updating cart count'));
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} transition-all duration-300 transform translate-x-full`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endpush

@push('styles')
<style>
/* Custom input number styling */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}

/* Quantity button hover effects */
.quantity-btn:hover {
    background-color: #f3f4f6;
}

.quantity-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.quantity-btn:disabled:hover {
    background-color: transparent;
}
</style>
@endpush