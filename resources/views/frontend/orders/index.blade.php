@extends('layouts.frontend')

@section('title', 'My Orders - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('profile.index') }}" class="hover:text-red-500">Profile</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Orders</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- FIXED: Sidebar Filter -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg border p-6">
                <h3 class="text-lg font-semibold mb-4">Filter Orders</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">All Orders</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <select id="date-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">All Time</option>
                            <option value="7days" {{ request('date_range') === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="30days" {{ request('date_range') === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="90days" {{ request('date_range') === '90days' ? 'selected' : '' }}>Last 90 Days</option>
                            <option value="1year" {{ request('date_range') === '1year' ? 'selected' : '' }}>Last Year</option>
                        </select>
                    </div>

                    <!-- ADDED: Payment Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                        <select id="payment-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">All Payments</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="pending_verification" {{ request('payment_status') === 'pending_verification' ? 'selected' : '' }}>Under Verification</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <!-- ADDED: Total Amount Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                        <select id="amount-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">All Amounts</option>
                            <option value="0-500000" {{ request('amount_range') === '0-500000' ? 'selected' : '' }}>Under Rp 500.000</option>
                            <option value="500000-1000000" {{ request('amount_range') === '500000-1000000' ? 'selected' : '' }}>Rp 500K - Rp 1M</option>
                            <option value="1000000-5000000" {{ request('amount_range') === '1000000-5000000' ? 'selected' : '' }}>Rp 1M - Rp 5M</option>
                            <option value="5000000-999999999" {{ request('amount_range') === '5000000-999999999' ? 'selected' : '' }}>Above Rp 5M</option>
                        </select>
                    </div>

                    <!-- ADDED: Apply Filters Button -->
                    <div class="pt-4 space-y-2">
                        <button onclick="applyFilters()" 
                                class="w-full bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors">
                            Apply Filters
                        </button>
                        <button onclick="clearFilters()" 
                                class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors">
                            Clear All Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:w-3/4">
            <div class="bg-white rounded-lg border">
                <div class="p-6 border-b">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
                            <p class="text-gray-600 mt-1">Track and manage your orders</p>
                        </div>
                        
                        <!-- ADDED: Sort Options -->
                        <div class="mt-4 md:mt-0">
                            <select id="sort-select" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="highest_amount" {{ request('sort') === 'highest_amount' ? 'selected' : '' }}>Highest Amount</option>
                                <option value="lowest_amount" {{ request('sort') === 'lowest_amount' ? 'selected' : '' }}>Lowest Amount</option>
                            </select>
                        </div>
                    </div>

                    <!-- ADDED: Active Filters Display -->
                    @if(request()->hasAny(['status', 'date_range', 'payment_status', 'amount_range', 'sort']))
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        
                        @if(request('status'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Status: {{ ucfirst(request('status')) }}
                            <button onclick="removeFilter('status')" class="ml-1 text-red-600 hover:text-red-800">×</button>
                        </span>
                        @endif
                        
                        @if(request('date_range'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Date: {{ ucfirst(str_replace('days', ' Days', request('date_range'))) }}
                            <button onclick="removeFilter('date_range')" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                        </span>
                        @endif
                        
                        @if(request('payment_status'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Payment: {{ ucfirst(str_replace('_', ' ', request('payment_status'))) }}
                            <button onclick="removeFilter('payment_status')" class="ml-1 text-green-600 hover:text-green-800">×</button>
                        </span>
                        @endif
                        
                        @if(request('amount_range'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Amount: 
                            @php
                                $range = request('amount_range');
                                if ($range === '0-500000') echo 'Under 500K';
                                elseif ($range === '500000-1000000') echo '500K-1M';
                                elseif ($range === '1000000-5000000') echo '1M-5M';
                                elseif ($range === '5000000-999999999') echo 'Above 5M';
                            @endphp
                            <button onclick="removeFilter('amount_range')" class="ml-1 text-purple-600 hover:text-purple-800">×</button>
                        </span>
                        @endif
                        
                        <button onclick="clearFilters()" class="text-xs text-gray-500 hover:text-gray-700 underline">
                            Clear all
                        </button>
                    </div>
                    @endif
                </div>

                <!-- ADDED: Results Summary -->
                <div class="px-6 py-3 bg-gray-50 border-b">
                    <p class="text-sm text-gray-600">
                        @if($orders->total() > 0)
                            Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }} orders
                        @else
                            No orders found
                        @endif
                    </p>
                </div>

                <div class="divide-y">
                    @forelse($orders as $order)
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                            <div class="flex items-center space-x-4 mb-4 lg:mb-0">
                                <div>
                                    <p class="font-semibold text-gray-900">Order #{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="flex flex-col space-y-1">
                                    <!-- Order Status -->
                                    <div class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $order->status === 'pending' || $order->status === 'confirmed' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($order->status) }}
                                    </div>
                                    <!-- Payment Status -->
                                    <div class="px-2 py-1 rounded text-xs font-medium
                                        {{ $order->payment_status === 'paid' ? 'bg-green-50 text-green-700' : '' }}
                                        {{ $order->payment_status === 'pending' ? 'bg-yellow-50 text-yellow-700' : '' }}
                                        {{ $order->payment_status === 'pending_verification' ? 'bg-blue-50 text-blue-700' : '' }}
                                        {{ $order->payment_status === 'failed' ? 'bg-red-50 text-red-700' : '' }}">
                                        Payment: {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">{{ $order->formatted_total }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->items->count() }} items</p>
                                </div>
                                <a href="{{ route('orders.show', $order->id) }}" 
                                   class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                                    View Details
                                </a>
                            </div>
                        </div>

                        <!-- Order Items Preview -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($order->items->take(3) as $item)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded overflow-hidden">
                                    @if($item->product && $item->product->primary_image)
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
                                    <h4 class="font-medium text-sm line-clamp-2">{{ $item->product_name }}</h4>
                                    <p class="text-xs text-gray-600">Qty: {{ $item->quantity }}</p>
                                    <p class="text-sm font-semibold text-red-500">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            @endforeach
                            
                            @if($order->items->count() > 3)
                            <div class="flex items-center justify-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">+{{ $order->items->count() - 3 }} more items</p>
                            </div>
                            @endif
                        </div>

                        <!-- Order Actions -->
                        <div class="flex items-center justify-between mt-4 pt-4 border-t">
                            <div class="flex items-center space-x-4">
                                @if($order->status === 'delivered')
                                    <button onclick="downloadInvoice({{ $order->id }})" 
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-download mr-1"></i>Download Invoice
                                    </button>
                                @endif
                                
                                @if($order->status === 'pending')
                                    <button onclick="cancelOrder({{ $order->id }})" 
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-times mr-1"></i>Cancel Order
                                    </button>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                @if($order->status === 'delivered')
                                    <button onclick="reorder({{ $order->id }})" 
                                            class="bg-gray-100 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-200">
                                        <i class="fas fa-redo mr-1"></i>Reorder
                                    </button>
                                @endif
                                
                                @if(in_array($order->status, ['shipped', 'delivered']))
                                    <button onclick="trackOrder({{ $order->id }})" 
                                            class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm hover:bg-blue-200">
                                        <i class="fas fa-map-marker-alt mr-1"></i>Track Order
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-shopping-bag text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">
                            @if(request()->hasAny(['status', 'date_range', 'payment_status', 'amount_range']))
                                No orders match your filters
                            @else
                                No orders yet
                            @endif
                        </h3>
                        <p class="text-gray-500 mb-4">
                            @if(request()->hasAny(['status', 'date_range', 'payment_status', 'amount_range']))
                                Try adjusting your filter criteria or clear all filters.
                            @else
                                When you place your first order, it will appear here.
                            @endif
                        </p>
                        @if(request()->hasAny(['status', 'date_range', 'payment_status', 'amount_range']))
                            <button onclick="clearFilters()"
                                    class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors mr-4">
                                Clear Filters
                            </button>
                        @endif
                        <a href="{{ route('products.index') }}" 
                           class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors">
                            Start Shopping
                        </a>
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                <div class="p-6 border-t">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ENHANCED: Improved filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to all filter selects
    document.getElementById('status-filter').addEventListener('change', applyFilters);
    document.getElementById('date-filter').addEventListener('change', applyFilters);
    document.getElementById('payment-filter').addEventListener('change', applyFilters);
    document.getElementById('amount-filter').addEventListener('change', applyFilters);
    document.getElementById('sort-select').addEventListener('change', applyFilters);
});

function applyFilters() {
    const url = new URL(window.location.href);
    
    // Get filter values
    const status = document.getElementById('status-filter').value;
    const dateRange = document.getElementById('date-filter').value;
    const paymentStatus = document.getElementById('payment-filter').value;
    const amountRange = document.getElementById('amount-filter').value;
    const sort = document.getElementById('sort-select').value;
    
    // Clear existing parameters
    url.searchParams.delete('status');
    url.searchParams.delete('date_range');
    url.searchParams.delete('payment_status');
    url.searchParams.delete('amount_range');
    url.searchParams.delete('sort');
    url.searchParams.delete('page'); // Reset pagination
    
    // Set new parameters
    if (status) url.searchParams.set('status', status);
    if (dateRange) url.searchParams.set('date_range', dateRange);
    if (paymentStatus) url.searchParams.set('payment_status', paymentStatus);
    if (amountRange) url.searchParams.set('amount_range', amountRange);
    if (sort && sort !== 'newest') url.searchParams.set('sort', sort);
    
    // Apply filters
    window.location.href = url.toString();
}

function clearFilters() {
    const url = new URL(window.location.href);
    // Keep only the base URL
    url.search = '';
    window.location.href = url.toString();
}

function removeFilter(filterName) {
    const url = new URL(window.location.href);
    url.searchParams.delete(filterName);
    url.searchParams.delete('page'); // Reset pagination
    window.location.href = url.toString();
}

function downloadInvoice(orderId) {
    window.open(`/orders/${orderId}/invoice`, '_blank');
}

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
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
                setTimeout(() => location.reload(), 1000);
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
            setTimeout(() => window.location.href = '/cart', 1000);
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

function showNotification(message, type = 'success') {
    // Use global notification function if available
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
        return;
    }

    // Fallback notification
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
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
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

/* Custom select styling */
select:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* Active filter badges */
.filter-badge {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}

/* Hover effects */
.filter-remove-btn:hover {
    transform: scale(1.1);
}
</style>
@endpush