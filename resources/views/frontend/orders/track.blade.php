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
    <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 overflow-hidden mb-8 voucher-card">
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

        <!-- ENHANCED: Progress Tracker dengan Resi Info -->
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
                                Order Confirmed - Processing Soon
                                @break
                            @case('processing')
                                Processing Your Order
                                @break
                            @case('shipped')
                                📦 Shipped - Tracking Available
                                @break
                            @case('delivered')
                                ✅ Delivered Successfully
                                @break
                            @case('cancelled')
                                ❌ Order Cancelled
                                @break
                            @default
                                Unknown Status
                        @endswitch
                    </span>
                </div>
                
                <!-- Progress Steps -->
                <div class="flex items-center justify-between relative">
                    @php
                        $steps = [
                            ['key' => 'pending', 'label' => 'Order Placed', 'icon' => 'fas fa-shopping-cart', 'description' => 'Awaiting confirmation'],
                            ['key' => 'confirmed', 'label' => 'Admin Confirmed', 'icon' => 'fas fa-user-check', 'description' => 'Payment verified'],
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

                <!-- UPDATED: Step-specific notices dengan resi info -->
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
                                Great! Your order has been reviewed and confirmed. We're now preparing your items for shipment.
                                You'll receive tracking information once the package is handed over to the courier.
                            </p>
                        </div>
                    </div>
                </div>
                @elseif($order->status === 'shipped' && $order->hasTrackingInfo())
                <!-- ENHANCED: Tracking Info Card ketika sudah shipped -->
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-truck text-green-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <h4 class="font-medium text-green-800 mb-3">📦 Package Shipped with Tracking!</h4>
                            
                            <!-- Tracking Number Display -->
                            <div class="bg-white border border-green-200 rounded-lg p-4 mb-4">
                                <div class="text-center">
                                    <p class="text-sm text-green-700 mb-2">Your Tracking Number:</p>
                                    <div class="flex items-center justify-center space-x-2">
                                        <code class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-xl font-bold font-mono letter-spacing-2">
                                            {{ $order->tracking_number }}
                                        </code>
                                        <button onclick="copyToClipboard('{{ $order->tracking_number }}')" 
                                                class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition-colors"
                                                title="Copy tracking number">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <p class="text-sm text-green-600 mt-2">
                                        <strong>Courier:</strong> {{ $order->courier }} | 
                                        <strong>Shipped:</strong> {{ $order->shipped_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>

                            @if($order->courier_tracking_url)
                            <!-- Direct Track Button -->
                            <div class="text-center mb-4">
                                <a href="{{ $order->courier_tracking_url }}" 
                                   target="_blank"
                                   class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Track on {{ $order->courier }} Website
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @elseif($order->status === 'delivered')
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-green-600 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-green-800 mb-1">🎉 Order Delivered Successfully!</h4>
                            <p class="text-sm text-green-700">
                                Your order was delivered on {{ $order->delivered_at->format('M d, Y g:i A') }}.
                                Your invoice is now available for download.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- ENHANCED: Tracking Tutorial Section -->
        @if($order->status === 'shipped' && $order->hasTrackingInfo())
        <div class="p-6 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4">🔍 How to Track Your Package</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Step by Step Guide -->
                <div>
                    <h4 class="font-medium text-gray-800 mb-3">📋 Tracking Steps:</h4>
                    <ol class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start">
                            <span class="bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</span>
                            Copy your tracking number: <code class="bg-gray-100 px-2 py-1 rounded ml-1">{{ $order->tracking_number }}</code>
                        </li>
                        <li class="flex items-start">
                            <span class="bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</span>
                            Visit {{ $order->courier }} official website
                        </li>
                        <li class="flex items-start">
                            <span class="bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</span>
                            Find "Track Package" or "Lacak Kiriman" section
                        </li>
                        <li class="flex items-start">
                            <span class="bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">4</span>
                            Enter your tracking number and check status
                        </li>
                    </ol>
                </div>

                <!-- Courier Contact Info -->
                <div>
                    <h4 class="font-medium text-gray-800 mb-3">📞 {{ $order->courier }} Contact Info:</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm">
                        @switch($order->courier)
                            @case('JNE')
                                <p><strong>Customer Service:</strong> 150 JNE (536)</p>
                                <p><strong>Website:</strong> <a href="https://www.jne.co.id/id/tracking/trace/{{ $order->tracking_number }}" target="_blank" class="text-blue-600 hover:underline">jne.co.id</a></p>
                                <p><strong>WhatsApp:</strong> 0811-1-562-562</p>
                                @break
                            @case('POS')
                                <p><strong>Customer Service:</strong> 161</p>
                                <p><strong>Website:</strong> <a href="https://www.posindonesia.co.id/id/tracking/{{ $order->tracking_number }}" target="_blank" class="text-blue-600 hover:underline">posindonesia.co.id</a></p>
                                @break
                            @case('TIKI')
                                <p><strong>Customer Service:</strong> (021) 7918-1888</p>
                                <p><strong>Website:</strong> <a href="https://www.tiki.id/id/tracking/{{ $order->tracking_number }}" target="_blank" class="text-blue-600 hover:underline">tiki.id</a></p>
                                @break
                            @case('JNT')
                                <p><strong>Customer Service:</strong> (021) 2927-8888</p>
                                <p><strong>Website:</strong> <a href="https://www.jet.co.id/track/{{ $order->tracking_number }}" target="_blank" class="text-blue-600 hover:underline">jet.co.id</a></p>
                                @break
                            @case('SICEPAT')
                                <p><strong>Customer Service:</strong> (021) 5020-0050</p>
                                <p><strong>Website:</strong> <a href="https://www.sicepat.com/checkAwb/{{ $order->tracking_number }}" target="_blank" class="text-blue-600 hover:underline">sicepat.com</a></p>
                                @break
                            @case('ANTERAJA')
                                <p><strong>Customer Service:</strong> 1500-389</p>
                                <p><strong>Website:</strong> <a href="https://www.anteraja.id/tracking/{{ $order->tracking_number }}" target="_blank" class="text-blue-600 hover:underline">anteraja.id</a></p>
                                @break
                            @default
                                <p>Please contact {{ $order->courier }} directly for tracking information.</p>
                        @endswitch
                        
                        <div class="mt-3 p-2 bg-blue-50 rounded text-xs text-blue-700">
                            💡 <strong>Tip:</strong> Tracking updates may take 2-4 hours to appear after shipment.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- No Tracking Info Yet -->
        <div class="p-6 border-b border-gray-200">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                <i class="fas fa-truck text-gray-400 text-2xl mb-2"></i>
                <h4 class="font-medium text-gray-600 mb-1">Tracking Information Not Available Yet</h4>
                <p class="text-sm text-gray-500">
                    @if($order->status === 'pending')
                        Tracking details will be provided after order confirmation and processing.
                    @elseif($order->status === 'confirmed')
                        Your order is confirmed. Tracking information will be provided once shipped.
                    @elseif($order->status === 'processing')
                        Your order is being processed. Tracking information will be provided once shipped.
                    @else
                        Tracking information will be updated once your order is shipped.
                    @endif
                </p>
            </div>
        </div>
        @endif

        @php
            // Smart detection of payment status
            $effectivePaymentStatus = $order->payment_status;
            $hasPaymentProof = !empty($order->payment_proof);
            
            // Jika ada payment proof tapi status masih pending = sedang verifikasi
            if ($hasPaymentProof && $order->payment_status === 'pending') {
                $effectivePaymentStatus = 'pending_verification';
            }
        @endphp

        <!-- Payment Status -->
        @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending' && !$hasPaymentProof)
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

        @elseif($effectivePaymentStatus === 'pending_verification')
        <div class="p-6 border-b border-gray-200">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-blue-600 mr-3"></i>
                    <div class="flex-1">
                        <h3 class="font-medium text-blue-800">Payment Under Verification</h3>
                        <p class="text-sm text-blue-700">
                            We received your payment proof and are verifying it. This usually takes 24 hours.
                        </p>
                        @if($order->payment_proof_uploaded_at)
                            <p class="text-xs text-blue-600 mt-1">
                                Uploaded: {{ $order->payment_proof_uploaded_at->format('M d, Y g:i A') }}
                            </p>
                        @endif
                        
                        <!-- Allow re-upload jika diperlukan -->
                        <div class="mt-3">
                            <button onclick="showPaymentUpload()" 
                                    class="text-blue-600 hover:text-blue-800 text-sm underline">
                                Update Payment Proof
                            </button>
                        </div>
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
        
        @if($order->hasTrackingInfo())
        <a href="{{ $order->courier_tracking_url }}" target="_blank"
           class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors text-center">
            <i class="fas fa-external-link-alt mr-2"></i>Track on {{ $order->courier }}
        </a>
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
                    <p class="text-xs text-gray-500 mt-1">Max file size: 2MB. Formats: JPG, PNG, GIF</p>
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
// Auto-refresh untuk check status update
let refreshInterval;

document.addEventListener('DOMContentLoaded', function() {
    // Check if order is still pending/processing, auto refresh every 30 seconds
    @if(in_array($order->status, ['pending', 'confirmed', 'processing']))
    refreshInterval = setInterval(function() {
        // Check for status updates
        fetch(`/api/orders/{{ $order->id }}/payment-status`)
        .then(response => response.json())
        .then(data => {
            // If status changed, reload page to show updated info
            if (data.order_status !== '{{ $order->status }}') {
                location.reload();
            }
        })
        .catch(error => console.log('Status check error:', error));
    }, 30000); // 30 seconds
    @endif
});

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
        showNotification('Tracking number copied to clipboard! 📋', 'success');
    }).catch(function() {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Tracking number copied to clipboard! 📋', 'success');
    });
}

// ENHANCED: Payment upload form submission dengan better error handling
document.getElementById('paymentUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    const fileInput = this.querySelector('input[name="payment_proof"]');
    
    // Enhanced validation
    if (!fileInput.files[0]) {
        showNotification('Please select a payment proof image', 'error');
        return;
    }
    
    const file = fileInput.files[0];
    
    // File size check (2MB)
    if (file.size > 2 * 1024 * 1024) {
        showNotification('File size must be less than 2MB', 'error');
        return;
    }
    
    // File type check
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Only JPG, PNG, and GIF images are allowed', 'error');
        return;
    }
    
    // Show uploading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
    submitBtn.disabled = true;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showNotification('CSRF token not found. Please refresh the page.', 'error');
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    // Debug log
    console.log('Uploading payment proof for order: {{ $order->id }}');
    console.log('File details:', {
        name: file.name,
        size: file.size,
        type: file.type
    });
    
    fetch(`/orders/{{ $order->id }}/upload-payment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(async response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers.entries()]);
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Server returned non-JSON response. Check Laravel logs.');
        }
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (response.ok && data.success) {
            showNotification(data.message, 'success');
            hidePaymentUpload();
            
            // Reload page after 2 seconds to show updated status
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            // Handle different error types
            let errorMessage = data.message || 'Error uploading payment proof';
            
            if (data.errors) {
                // Validation errors
                const errorList = Object.values(data.errors).flat();
                errorMessage = errorList.join(', ');
            }
            
            console.error('Upload error:', data);
            showNotification(errorMessage, 'error');
        }
    })
    .catch(error => {
        console.error('Network/Parse error:', error);
        showNotification('Network error or server issue. Please check your connection and try again.', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Enhanced notification function
function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.upload-notification');
    existingNotifications.forEach(notif => notif.remove());

    const notification = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle',
        warning: 'fas fa-exclamation-triangle'
    };
    
    notification.className = `upload-notification fixed top-4 right-4 z-50 p-4 rounded-lg text-white ${colors[type]} transition-all duration-300 transform translate-x-full max-w-sm`;
    notification.innerHTML = `
        <div class="flex items-start">
            <i class="${icons[type]} mr-3 mt-0.5"></i>
            <div class="flex-1">
                <span class="text-sm">${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Slide in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }
    }, 5000);
}

// Clean up interval when leaving page
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
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

/* Enhanced tracking number styling */
.letter-spacing-2 {
    letter-spacing: 2px;
}

/* Progress step animations */
.progress-step {
    transition: all 0.3s ease-in-out;
}

.progress-step.completed {
    transform: scale(1.1);
}

/* Tracking info highlight animation */
@keyframes highlight {
    0% { background-color: #f0fdf4; }
    50% { background-color: #dcfce7; }
    100% { background-color: #f0fdf4; }
}

.tracking-highlight {
    animation: highlight 2s ease-in-out;
}

/* Copy button hover effect */
button[onclick*="copyToClipboard"]:hover {
    transform: scale(1.05);
}

/* Responsive improvements */
@media (max-width: 640px) {
    .voucher-card {
        border-radius: 0;
        border-left: none;
        border-right: none;
    }
}
</style>
@endpush