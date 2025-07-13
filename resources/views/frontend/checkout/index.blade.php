@extends('layouts.frontend')

@section('title', 'Checkout - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('cart.index') }}" class="hover:text-red-500">Cart</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Checkout</li>
        </ol>
    </nav>

    <form method="POST" action="{{ route('checkout.process') }}" id="checkout-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Shipping Information -->
                <div class="bg-white rounded-lg border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Shipping Information</h2>
                    
                   <!-- GANTI bagian input fields di shipping information dengan ini: -->

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
        <input type="text" name="shipping_first_name" 
               value="{{ old('shipping_first_name', $user->first_name ?? '') }}" 
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
               required>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
        <input type="text" name="shipping_last_name" 
               value="{{ old('shipping_last_name', $user->last_name ?? '') }}" 
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
               required>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
        <input type="email" name="shipping_email" 
               value="{{ old('shipping_email', $user->email ?? '') }}" 
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
               required>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
        <input type="tel" name="shipping_phone" 
               value="{{ old('shipping_phone', $user->phone ?? '') }}" 
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
               required>
    </div>
    
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
        <textarea name="shipping_address" rows="3" 
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
                  required>{{ old('shipping_address', '') }}</textarea>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
        <input type="text" name="shipping_city" 
               value="{{ old('shipping_city', '') }}" 
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
               required>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">State/Province *</label>
        <input type="text" name="shipping_state" 
               value="{{ old('shipping_state', '') }}" 
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
               required>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal Code *</label>
        <input type="text" name="shipping_zip" 
               value="{{ old('shipping_zip', '') }}" 
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
               required>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
        <select name="shipping_country" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" 
                required>
            <option value="Indonesia" {{ old('shipping_country', 'Indonesia') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
            <option value="Malaysia" {{ old('shipping_country') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
            <option value="Singapore" {{ old('shipping_country') == 'Singapore' ? 'selected' : '' }}>Singapore</option>
        </select>
    </div>
</div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Method</h2>
                    
                    <!-- Hidden input untuk default payment method -->
                    <input type="hidden" name="payment_method" value="bank_transfer">
                    
                    <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                        <div class="flex items-center">
                            <i class="fas fa-university text-blue-600 mr-3"></i>
                            <div>
                                <span class="font-medium text-blue-900">Bank Transfer</span>
                                <p class="text-sm text-blue-700">Transfer payment to our bank account after order confirmation</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-sm text-gray-600">
                        <p><i class="fas fa-info-circle mr-1"></i> You will receive bank transfer instructions after placing your order.</p>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white rounded-lg border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Notes (Optional)</h2>
                    <textarea name="notes" rows="4" placeholder="Special instructions for your order..." 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border p-6 sticky top-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                    
                    <!-- Cart Items -->
                    <div class="space-y-4 mb-4">
                        @foreach($cartItems as $item)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded overflow-hidden">
                                @if($item->product->primary_image)
                                    <img src="{{ Storage::url($item->product->primary_image->image_path) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-sm line-clamp-2">{{ $item->product->name }}</h4>
                                <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                <p class="text-sm font-semibold text-red-500">{{ $item->formatted_total ?? 'Rp ' . number_format(($item->price ?? $item->product->price) * $item->quantity, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Order Totals -->
                    <div class="border-t pt-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ $cartItems->count() }} items)</span>
                            <span class="text-gray-900">{{ $cartSummary['subtotal'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="text-gray-900">{{ $cartSummary['shipping'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax (PPN 11%)</span>
                            <span class="text-gray-900">{{ $cartSummary['tax'] }}</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-900">Total</span>
                                <span class="text-lg font-semibold text-gray-900">{{ $cartSummary['total'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mt-6">
                        <label class="flex items-start">
                            <input type="checkbox" id="terms" class="mt-1 text-red-500 focus:ring-red-500" required>
                            <span class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-red-500 hover:text-red-600">Terms and Conditions</a> 
                                and <a href="#" class="text-red-500 hover:text-red-600">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Place Order Button -->
                    <button type="submit" id="place-order-btn"
                            class="w-full bg-red-500 text-white py-3 rounded-lg hover:bg-red-600 transition-colors font-medium mt-6 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btn-text">Place Order</span>
                        <span id="btn-loading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                        </span>
                    </button>

                    <!-- Security Info -->
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <i class="fas fa-lock"></i>
                            <span>Your payment information is secure and encrypted</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');
    const termsCheckbox = document.getElementById('terms');

    // Enable/disable place order button based on terms checkbox
    termsCheckbox.addEventListener('change', function() {
        placeOrderBtn.disabled = !this.checked;
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            showNotification('Please fill in all required fields', 'error');
            return;
        }

        // Show loading state
        placeOrderBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');

        // Submit the form
        this.submit();
    });

    // Auto-fill billing address if same as shipping
    const sameAsShippingCheckbox = document.getElementById('same-as-shipping');
    if (sameAsShippingCheckbox) {
        sameAsShippingCheckbox.addEventListener('change', function() {
            const shippingFields = document.querySelectorAll('[name^="shipping_"]');
            const billingFields = document.querySelectorAll('[name^="billing_"]');
            
            if (this.checked) {
                shippingFields.forEach((field, index) => {
                    if (billingFields[index]) {
                        billingFields[index].value = field.value;
                    }
                });
            }
        });
    }
});

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
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Radio button styling */
input[type="radio"]:checked {
    background-color: #ef4444;
    border-color: #ef4444;
}

/* Loading animation */
@keyframes spin {
    to { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 2s linear infinite;
}
</style>
@endpush