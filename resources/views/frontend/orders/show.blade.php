@extends('layouts.frontend')

@section('title', 'Order #' . $order->order_number . ' - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('profile.index') }}" class="hover:text-red-500">Profile</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('orders.index') }}" class="hover:text-red-500">Orders</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Order #{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Header -->
            <div class="bg-white rounded-lg border p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                        <p class="text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="flex items-center space-x-4 mt-4 md:mt-0">
                        <div class="px-4 py-2 rounded-full text-sm font-medium
                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($order->status) }}
                        </div>
                        @if($order->status === 'delivered')
                        <button onclick="downloadInvoice({{ $order->id }})" 
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                            <i class="fas fa-download mr-2"></i>Download Invoice
                        </button>
                        @endif
                    </div>
                </div>

                <!-- UPDATED: Order Status Notice -->
                @if($order->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-clock text-yellow-600 mt-1 mr-3"></i>
                        <div>
                            <h3 class="font-medium text-yellow-800 mb-1">Order Awaiting Confirmation</h3>
                            <p class="text-sm text-yellow-700 mb-2">
                                Your order has been successfully placed and is waiting for admin confirmation. 
                                Our team will review and confirm your order within 24 hours.
                            </p>
                            <div class="text-xs text-yellow-600">
                                <p>• You will receive an email notification once your order is confirmed</p>
                                <p>• Payment processing will begin after confirmation</p>
                                <p>• You can cancel this order before confirmation if needed</p>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($order->status === 'confirmed')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <h3 class="font-medium text-blue-800 mb-1">Order Confirmed</h3>
                            <p class="text-sm text-blue-700">
                                Great! Your order has been confirmed by our admin team and is now being processed.
                                @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
                                Please complete your payment to proceed with shipping.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- UPDATED: Order Progress -->
                <div class="relative">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900">Order Progress</span>
                        <span class="text-sm text-gray-600">
                            @switch($order->status)
                                @case('pending')
                                    Awaiting Confirmation
                                    @break
                                @case('confirmed')
                                    Order Confirmed
                                    @break
                                @case('processing')
                                    Processing
                                    @break
                                @case('shipped')
                                    Shipped
                                    @break
                                @case('delivered')
                                    Delivered
                                    @break
                                @case('cancelled')
                                    Cancelled
                                    @break
                                @default
                                    Unknown
                            @endswitch
                        </span>
                    </div>
                    
                    @php
                        $progressPercentage = match($order->status) {
                            'pending' => 20,
                            'confirmed' => 40,
                            'processing' => 60,
                            'shipped' => 80,
                            'delivered' => 100,
                            'cancelled' => 0,
                            default => 0
                        };
                    @endphp
                    
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                        <span>Order Placed</span>
                        <span>Confirmed</span>
                        <span>Processing</span>
                        <span>Shipped</span>
                        <span>Delivered</span>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg border">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-semibold text-gray-900">Order Items ({{ $order->items->count() }})</h2>
                </div>
                <div class="divide-y">
                    @foreach($order->items as $item)
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
                                @if($item->product && $item->product->images && $item->product->images->count() > 0)
                                    <img src="{{ Storage::url($item->product->images->first()->image_path) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">
                                    @if($item->product)
                                        <a href="{{ route('products.show', $item->product->slug) }}" 
                                           class="hover:text-red-500">{{ $item->product->name }}</a>
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-600 mb-2">SKU: {{ $item->product_sku }}</p>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-600">Qty: {{ $item->quantity }}</span>
                                    <span class="text-sm font-semibold text-red-500">Rp {{ number_format($item->price, 0, ',', '.') }} each</span>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($item->total, 0, ',', '.') }}</p>
                                @if($order->status === 'delivered')
                                <button onclick="reviewProduct({{ $item->product->id }})" 
                                        class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                                    Write Review
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- UPDATED: Order Timeline -->
            <div class="bg-white rounded-lg border">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-semibold text-gray-900">Order Timeline</h2>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @php
                            // Determine which steps are completed based on order status
                            $currentStepIndex = match($order->status) {
                                'pending' => 0,
                                'confirmed' => 1,
                                'processing' => 2,
                                'shipped' => 3,
                                'delivered' => 4,
                                'cancelled' => -1,
                                default => 0
                            };
                            
                            $timeline = [
                                [
                                    'status' => 'placed', 
                                    'title' => 'Order Placed', 
                                    'description' => 'Your order has been placed successfully and is awaiting confirmation', 
                                    'date' => $order->created_at,
                                    'completed' => true
                                ],
                                [
                                    'status' => 'confirmed', 
                                    'title' => 'Order Confirmed', 
                                    'description' => 'Admin has reviewed and confirmed your order', 
                                    'date' => $order->confirmed_at ?? null,
                                    'completed' => $currentStepIndex >= 1
                                ],
                                [
                                    'status' => 'processing', 
                                    'title' => 'Processing', 
                                    'description' => 'Your order is being prepared for shipment', 
                                    'date' => $order->processing_at ?? null,
                                    'completed' => $currentStepIndex >= 2
                                ],
                                [
                                    'status' => 'shipped', 
                                    'title' => 'Shipped', 
                                    'description' => 'Your order has been shipped and is on the way', 
                                    'date' => $order->shipped_at ?? null,
                                    'completed' => $currentStepIndex >= 3
                                ],
                                [
                                    'status' => 'delivered', 
                                    'title' => 'Delivered', 
                                    'description' => 'Your order has been delivered successfully', 
                                    'date' => $order->delivered_at ?? null,
                                    'completed' => $currentStepIndex >= 4
                                ],
                            ];
                            @endphp
                            
                            @foreach($timeline as $index => $event)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 
                                        {{ $event['completed'] ? 'bg-red-200' : 'bg-gray-200' }}" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                {{ $event['completed'] ? 'bg-red-500' : 'bg-gray-400' }}">
                                                @if($event['completed'])
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                @else
                                                    <i class="fas fa-clock text-white text-xs"></i>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm font-medium {{ $event['completed'] ? 'text-gray-900' : 'text-gray-500' }}">
                                                    {{ $event['title'] }}
                                                </p>
                                                <p class="text-sm {{ $event['completed'] ? 'text-gray-700' : 'text-gray-400' }}">
                                                    {{ $event['description'] }}
                                                </p>
                                                @if($event['status'] === 'confirmed' && !$event['completed'])
                                                <p class="text-xs text-yellow-600 mt-1">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Waiting for admin confirmation
                                                </p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                @if($event['completed'] && $event['date'])
                                                    {{ $event['date']->format('M d, Y g:i A') }}
                                                @else
                                                    <span class="text-gray-400">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary Card -->
            <div class="bg-white rounded-lg border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">Rp {{ number_format($order->subtotal ?? $order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="text-gray-900">
                            @if(($order->shipping_cost ?? 0) > 0)
                                Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                            @else
                                FREE
                            @endif
                        </span>
                    </div>
                    @if(($order->discount_amount ?? 0) > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Discount</span>
                        <span class="text-red-500">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax (11%)</span>
                        <span class="text-gray-900">Rp {{ number_format(($order->tax_amount ?? $order->total_amount * 0.11), 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold text-gray-900">Total</span>
                            <span class="text-lg font-semibold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="bg-white rounded-lg border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Information</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Shipping Address</label>
                        <div class="mt-1 text-sm text-gray-900">
                            <p>{{ $order->customer_name }}</p>
                            <p>{{ $order->shipping_address }}</p>
                            <p>{{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
                        </div>
                    </div>
                    
                    @if($order->tracking_number ?? false)
                    <div>
                        <label class="text-sm font-medium text-gray-700">Tracking Number</label>
                        <div class="mt-1">
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $order->tracking_number }}</code>
                        </div>
                    </div>
                    @endif
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700">Shipping Method</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->shipping_method ?? 'Standard Shipping' }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white rounded-lg border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Payment Method</label>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700">Payment Status</label>
                        <span class="mt-1 inline-block px-2 py-1 text-xs font-medium rounded-full
                            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                        </span>
                    </div>
                    
                    @if($order->payment_reference)
                    <div>
                        <label class="text-sm font-medium text-gray-700">Payment Reference</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $order->payment_reference }}</p>
                    </div>
                    @endif

                    <!-- ADDED: Payment Instructions for pending orders -->
                    @if($order->status === 'confirmed' && $order->payment_status === 'pending' && $order->payment_method === 'bank_transfer')
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mt-3">
                        <p class="text-xs text-yellow-800 font-medium mb-2">Payment Required</p>
                        <p class="text-xs text-yellow-700">Please complete your payment to proceed with order processing.</p>
                        <button onclick="window.location.href='/orders/{{ $order->id }}/track'" 
                                class="mt-2 text-xs bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700">
                            Complete Payment
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                
                <div class="space-y-3">
                    @if($order->status === 'pending')
                    <button onclick="cancelOrder({{ $order->id }})" 
                            class="w-full bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel Order
                    </button>
                    <div class="text-xs text-gray-500 text-center">
                        You can cancel this order before admin confirmation
                    </div>
                    @endif
                    
                    @if($order->status === 'delivered')
                    <button onclick="reorder({{ $order->id }})" 
                            class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded hover:bg-gray-200 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Reorder
                    </button>
                    
                    <button onclick="downloadInvoice({{ $order->id }})" 
                            class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors">
                        <i class="fas fa-download mr-2"></i>Download Invoice
                    </button>
                    @endif
                    
                    @if(in_array($order->status, ['shipped', 'delivered']))
                    <button onclick="trackOrder({{ $order->id }})" 
                            class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors">
                        <i class="fas fa-map-marker-alt mr-2"></i>Track Order
                    </button>
                    @endif
                    
                    <a href="{{ route('orders.index') }}" 
                       class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded hover:bg-gray-200 transition-colors block text-center">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function downloadInvoice(orderId) {
    // Only allow invoice download for delivered orders
    @if($order->status === 'delivered')
        window.open(`/orders/${orderId}/invoice`, '_blank');
    @else
        showNotification('Invoice is only available after order delivery', 'info');
    @endif
}

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        fetch(`/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Order cancelled successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'Error cancelling order', 'error');
            }
        })
        .catch(error => {
            showNotification('Error cancelling order', 'error');
        });
    }
}

function reorder(orderId) {
    fetch(`/orders/${orderId}/reorder`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Items added to cart', 'success');
            setTimeout(() => window.location.href = '/cart', 1500);
        } else {
            showNotification(data.message || 'Error reordering', 'error');
        }
    })
    .catch(error => {
        showNotification('Error reordering', 'error');
    });
}

function trackOrder(orderId) {
    window.location.href = `/orders/${orderId}/track`;
}

function reviewProduct(productId) {
    window.location.href = `/products/${productId}?review=true`;
}

function showNotification(message, type = 'success') {
    // Use global notification function if available
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
        return;
    }

    // Fallback notification
    const notification = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white ${colors[type]} transition-all duration-300 transform translate-x-full`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}
</script>
@endpush