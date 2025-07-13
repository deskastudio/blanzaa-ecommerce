@extends('layouts.frontend')

@section('title', 'Track Order #' . $order->order_number . ' - Exclusive Electronics Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('orders.index') }}" class="hover:text-red-500">Orders</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Track Order</li>
        </ol>
    </nav>

    <!-- E-Voucher Card -->
    <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 overflow-hidden mb-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Order E-Voucher</h1>
                    <p class="opacity-90">Track your order status and delivery</p>
                </div>
                <div class="text-right">
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                        <i class="fas fa-qrcode text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-600 uppercase tracking-wide">Order Number</label>
                    <p class="text-lg font-bold text-gray-900">{{ $order->order_number }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 uppercase tracking-wide">Order Date</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total Amount</label>
                    <p class="text-lg font-bold text-red-500">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-center">
                <div class="px-8 py-4 rounded-full text-xl font-bold
                    {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                    {{ $order->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ strtoupper($order->status) }}
                </div>
            </div>
        </div>

        <!-- UPDATED: Progress Tracker with Admin Confirmation -->
        <div class="p-6 border-b border-gray-200">
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-900">Order Progress</span>
                    <span class="text-sm text-gray-600">
                        @switch($order->status)
                            @case('pending')
                                Awaiting Admin Confirmation
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
                
                <!-- Progress Steps -->
                <div class="flex items-center justify-between relative">
                    @php
                        $steps = [
                            ['key' => 'pending', 'label' => 'Order Placed', 'icon' => 'fas fa-shopping-cart', 'description' => 'Awaiting confirmation'],
                            ['key' => 'confirmed', 'label' => 'Admin Confirmed', 'icon' => 'fas fa-user-check', 'description' => 'Order verified'],
                            ['key' => 'processing', 'label' => 'Processing', 'icon' => 'fas fa-cogs', 'description' => 'Preparing items'],
                            ['key' => 'shipped', 'label' => 'Shipped', 'icon' => 'fas fa-truck', 'description' => 'On the way'],
                            ['key' => 'delivered', 'label' => 'Delivered', 'icon' => 'fas fa-check-circle', 'description' => 'Completed']
                        ];
                        
                        $currentStepIndex = match($order->status) {
                            'pending' => 0,
                            'confirmed' => 1,
                            'processing' => 2,
                            'shipped' => 3,
                            'delivered' => 4,
                            'cancelled' => -1,
                            default => 0
                        };
                    @endphp
                    
                    <!-- Progress Line -->
                    <div class="absolute top-6 left-0 right-0 h-1 bg-gray-200 rounded-full">
                        <div class="h-full bg-red-500 rounded-full transition-all duration-500" 
                             style="width: {{ $order->status === 'cancelled' ? '0' : (($currentStepIndex + 1) / count($steps)) * 100 }}%"></div>
                    </div>
                    
                    @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center relative z-10">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-2 transition-all duration-300
                            {{ $index <= $currentStepIndex && $order->status !== 'cancelled' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <i class="{{ $step['icon'] }}"></i>
                        </div>
                        <span class="text-xs text-center font-medium {{ $index <= $currentStepIndex && $order->status !== 'cancelled' ? 'text-red-600' : 'text-gray-500' }}">
                            {{ $step['label'] }}
                        </span>
                        <span class="text-xs text-center text-gray-400 mt-1">
                            {{ $step['description'] }}
                        </span>
                    </div>
                    @endforeach
                </div>

                <!-- ADDED: Step-specific notices -->
                @if($order->status === 'pending')
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-clock text-yellow-600 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-yellow-800 mb-1">Waiting for Admin Confirmation</h4>
                            <p class="text-sm text-yellow-700 mb-2">
                                Your order is in our queue for review. Our admin team will confirm your order within 24 hours.
                            </p>
                            <ul class="text-xs text-yellow-600 space-y-1">
                                <li>• Order verification in progress</li>
                                <li>• Stock availability check</li>
                                <li>• Payment method validation</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @elseif($order->status === 'confirmed')
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-blue-800 mb-1">Order Confirmed by Admin</h4>
                            <p class="text-sm text-blue-700">
                                Great! Your order has been reviewed and confirmed. 
                                @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
                                Please complete your payment to proceed with processing.
                                @else
                                We're now preparing your items for shipment.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Status -->
        @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
        <div class="p-6 border-b border-gray-200">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                    <div class="flex-1">
                        <h3 class="font-medium text-yellow-800 mb-2">Payment Required</h3>
                        <p class="text-sm text-yellow-700 mb-3">
                            @if($order->status === 'confirmed')
                                Your order is confirmed! Please complete your payment to start processing.
                            @else
                                Please complete your payment. Processing will begin after admin confirmation.
                            @endif
                        </p>
                        
                        <!-- Bank Transfer Details -->
                        <div class="bg-white rounded p-3 mb-3">
                            <h4 class="font-medium text-gray-900 mb-2">Bank Transfer Details</h4>
                            <div class="text-sm space-y-1">
                                <p><strong>Bank:</strong> Bank Central Asia (BCA)</p>
                                <p><strong>Account Number:</strong> 1234567890</p>
                                <p><strong>Account Name:</strong> Exclusive Electronics Store</p>
                                <p><strong>Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                <p><strong>Reference:</strong> {{ $order->order_number }}</p>
                            </div>
                        </div>
                        
                        <!-- Upload Payment Proof -->
                        <div>
                            <button onclick="showPaymentUpload()" 
                                    class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition-colors">
                                Upload Payment Proof
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @elseif($order->payment_status === 'pending_verification')
        <div class="p-6 border-b border-gray-200">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-blue-600 mr-3"></i>
                    <div>
                        <h3 class="font-medium text-blue-800">Payment Under Verification</h3>
                        <p class="text-sm text-blue-700">We are verifying your payment. This usually takes 24 hours.</p>
                    </div>
                </div>
            </div>
        </div>
        @elseif($order->payment_status === 'paid')
        <div class="p-6 border-b border-gray-200">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <div>
                        <h3 class="font-medium text-green-800">Payment Confirmed</h3>
                        <p class="text-sm text-green-700">Your payment has been confirmed and your order is being processed.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Shipping Information -->
        @if($order->status === 'shipped' || $order->status === 'delivered')
        <div class="p-6 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4">Shipping Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($order->tracking_number)
                <div>
                    <label class="text-sm font-medium text-gray-600">Tracking Number</label>
                    <div class="flex items-center mt-1">
                        <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $order->tracking_number }}</code>
                        <button onclick="copyToClipboard('{{ $order->tracking_number }}')" 
                                class="ml-2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                @endif
                
                <div>
                    <label class="text-sm font-medium text-gray-600">Estimated Delivery</label>
                    <p class="mt-1 text-gray-900">
                        {{ $order->estimated_delivery_date ? $order->estimated_delivery_date->format('M d, Y') : 'TBD' }}
                    </p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-600">Shipping Method</label>
                    <p class="mt-1 text-gray-900">{{ $order->shipping_method ?? 'Standard Shipping' }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-600">Courier</label>
                    <p class="mt-1 text-gray-900">{{ $order->courier ?? 'JNE' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Order Items -->
        <div class="p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Order Items ({{ $order->items->count() }})</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0 w-16 h-16 bg-white rounded overflow-hidden">
                        @if($item->product && $item->product->images->count() > 0)
                            <img src="{{ Storage::url($item->product->images->first()->image_path) }}" 
                                 alt="{{ $item->product_name }}" 
                                 class="w-full h-full object-contain">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">{{ $item->product_name }}</h4>
                        <p class="text-sm text-gray-600">SKU: {{ $item->product_sku }}</p>
                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                    </div>
                    
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">Rp {{ number_format($item->total, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">@ Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- UPDATED: Invoice Notice -->
        @if($order->status === 'delivered')
        <div class="bg-green-50 border-t border-green-200 p-4">
            <div class="flex items-center justify-center">
                <i class="fas fa-file-invoice text-green-600 mr-2"></i>
                <span class="text-sm text-green-800 font-medium">Invoice is now available for download</span>
            </div>
        </div>
        @else
        <div class="bg-gray-50 border-t border-gray-200 p-4">
            <div class="flex items-center justify-center">
                <i class="fas fa-info-circle text-gray-500 mr-2"></i>
                <span class="text-sm text-gray-600">Invoice will be available after order delivery</span>
            </div>
        </div>
        @endif

        <!-- Voucher Footer -->
        <div class="bg-gray-50 px-6 py-4 text-center border-t border-gray-200">
            <p class="text-sm text-gray-600 mb-2">Need help? Contact our customer service</p>
            <div class="flex items-center justify-center space-x-4 text-sm">
                <span class="flex items-center">
                    <i class="fas fa-phone mr-1"></i>
                    +62 21 1234 5678
                </span>
                <span class="flex items-center">
                    <i class="fas fa-envelope mr-1"></i>
                    support@exclusive-electronics.com
                </span>
            </div>
        </div>
    </div>

    <!-- UPDATED: Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button onclick="window.print()" 
                class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
            <i class="fas fa-print mr-2"></i>Print Voucher
        </button>
        
        @if($order->status === 'delivered')
        <a href="{{ route('orders.invoice', $order) }}" target="_blank"
           class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors text-center">
            <i class="fas fa-download mr-2"></i>Download Invoice
        </a>
        @else
        <button disabled 
                class="bg-gray-300 text-gray-500 px-6 py-3 rounded-lg cursor-not-allowed text-center">
            <i class="fas fa-download mr-2"></i>Invoice Available After Delivery
        </button>
        @endif
        
        <a href="{{ route('orders.show', $order) }}" 
           class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors text-center">
            <i class="fas fa-eye mr-2"></i>View Full Details
        </a>
        
        <a href="{{ route('orders.index') }}" 
           class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors text-center">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>
    </div>
</div>

<!-- Payment Upload Modal -->
<div id="paymentUploadModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Payment Proof</h3>
            <form id="paymentUploadForm" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Proof Image</label>
                    <input type="file" name="payment_proof" accept="image/*" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <p class="text-xs text-gray-500 mt-1">Max file size: 2MB. Formats: JPG, PNG</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hidePaymentUpload()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showPaymentUpload() {
    document.getElementById('paymentUploadModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function hidePaymentUpload() {
    document.getElementById('paymentUploadModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Tracking number copied to clipboard!', 'success');
    });
}

// Payment upload form submission
document.getElementById('paymentUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/orders/{{ $order->id }}/upload-payment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            hidePaymentUpload();
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotification(data.message || 'Error uploading payment proof', 'error');
        }
    })
    .catch(error => {
        showNotification('Error uploading payment proof', 'error');
    });
});

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

// Auto-refresh payment status
@if($order->payment_status === 'pending_verification')
setInterval(function() {
    fetch(`/api/orders/{{ $order->id }}/payment-status`)
    .then(response => response.json())
    .then(data => {
        if (data.payment_status === 'paid') {
            location.reload();
        }
    })
    .catch(error => console.log('Error checking payment status'));
}, 30000); // Check every 30 seconds
@endif
</script>
@endpush

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    .voucher-card, .voucher-card * {
        visibility: visible;
    }
    
    .voucher-card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    
    .no-print {
        display: none !important;
    }
}

.voucher-card {
    background: white;
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
}

/* Progress step animations */
.progress-step {
    transition: all 0.3s ease-in-out;
}

.progress-step.completed {
    transform: scale(1.1);
}
</style>
@endpush