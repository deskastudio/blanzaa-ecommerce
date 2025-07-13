@extends('layouts.frontend')

@section('title', 'Order Success - Exclusive Electronics Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Success Header -->
    <div class="text-center mb-8">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-4xl text-green-600"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Placed Successfully!</h1>
        <p class="text-gray-600">Thank you for your purchase. Your order has been received and is being processed.</p>
    </div>

    <!-- Order Details -->
    <div class="bg-white rounded-lg border p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Order #{{ $order->order_number }}</h2>
                <p class="text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Order Items -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Items Ordered</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded overflow-hidden">
                            @if($item->product && $item->product->primary_image)
                                <img src="{{ Storage::url($item->product->primary_image->image_path) }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="w-full h-full object-contain">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-sm">{{ $item->product_name }}</h4>
                            <p class="text-xs text-gray-500">SKU: {{ $item->product_sku }}</p>
                            <p class="text-xs text-gray-500">Qty: {{ $item->quantity }} × {{ $item->formatted_price }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm">{{ $item->formatted_total }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Totals -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Order Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">{{ $order->formatted_subtotal }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-900">{{ $order->formatted_shipping_cost }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span class="text-gray-900">{{ $order->formatted_tax }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Discount</span>
                        <span class="text-red-500">-{{ $order->formatted_discount }}</span>
                    </div>
                    @endif
                    <div class="border-t pt-2">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-900">Total</span>
                            <span class="font-semibold text-gray-900">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping & Payment Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Shipping Address -->
        <div class="bg-white rounded-lg border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Shipping Address</h3>
            <div class="text-sm text-gray-600">
                <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                <p>{{ $order->shipping_address }}</p>
                <p>{{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
                <p class="mt-2">
                    <span class="font-medium">Phone:</span> {{ $order->customer_phone }}
                </p>
                <p>
                    <span class="font-medium">Email:</span> {{ $order->customer_email }}
                </p>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="bg-white rounded-lg border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Payment Information</h3>
            <div class="text-sm text-gray-600">
                <p class="mb-2">
                    <span class="font-medium">Payment Method:</span> 
                    <span class="capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                </p>
                <p class="mb-2">
                    <span class="font-medium">Payment Status:</span>
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                        {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
                @if($order->payment_reference)
                <p>
                    <span class="font-medium">Reference:</span> {{ $order->payment_reference }}
                </p>
                @endif
            </div>

            @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h4 class="font-medium text-yellow-800 mb-2">Bank Transfer Instructions</h4>
                <div class="text-sm text-yellow-700">
                    <p class="mb-1"><strong>Bank:</strong> Bank Central Asia (BCA)</p>
                    <p class="mb-1"><strong>Account Number:</strong> 1234567890</p>
                    <p class="mb-1"><strong>Account Name:</strong> Exclusive Electronics Store</p>
                    <p class="mb-2"><strong>Amount:</strong> {{ $order->formatted_total }}</p>
                    <p class="text-xs">Please include your order number ({{ $order->order_number }}) in the transfer description.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Next Steps -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="font-semibold text-blue-900 mb-3">What happens next?</h3>
        <div class="space-y-2 text-sm text-blue-800">
            @if($order->payment_method === 'cod')
                <p>• Your order will be prepared for shipment</p>
                <p>• You will receive a tracking number via email once shipped</p>
                <p>• Payment will be collected upon delivery</p>
            @elseif($order->payment_method === 'bank_transfer')
                <p>• Complete payment using the bank transfer details above</p>
                <p>• Your order will be processed once payment is confirmed</p>
                <p>• You will receive updates via email and SMS</p>
            @else
                <p>• Your payment is being processed</p>
                <p>• You will receive order confirmation via email</p>
                <p>• Your order will be prepared for shipment</p>
            @endif
            <p>• Track your order anytime in your account dashboard</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('orders.show', $order) }}" 
           class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors text-center">
            <i class="fas fa-eye mr-2"></i>View Order Details
        </a>
        <a href="{{ route('orders.index') }}" 
           class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors text-center">
            <i class="fas fa-list mr-2"></i>View All Orders
        </a>
        <a href="{{ route('products.index') }}" 
           class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors text-center">
            <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
        </a>
    </div>

    <!-- Support Info -->
    <div class="text-center mt-8 text-sm text-gray-600">
        <p>Need help with your order? 
            <a href="#" class="text-red-500 hover:text-red-600">Contact our support team</a>
        </p>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Auto-refresh page status if payment is pending
@if($order->payment_status === 'pending' && $order->payment_method !== 'cod')
setTimeout(function() {
    // Check payment status every 30 seconds for bank transfer/e-wallet
    setInterval(function() {
        fetch(`/api/orders/{{ $order->id }}/payment-status`)
        .then(response => response.json())
        .then(data => {
            if (data.payment_status === 'paid') {
                location.reload();
            }
        })
        .catch(error => console.log('Error checking payment status'));
    }, 30000);
}, 5000);
@endif
</script>
@endpush