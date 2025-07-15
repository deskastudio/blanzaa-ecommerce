@extends('layouts.frontend')

@section('title', 'Featured Products - Blanzaa Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('products.index') }}" class="hover:text-red-500">Products</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Featured Products</li>
        </ol>
    </nav>

    <!-- Featured Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-star text-2xl text-white"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    Featured Products
                    <span class="ml-3 text-sm bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full font-semibold">SPECIAL</span>
                </h1>
                <p class="text-gray-600">{{ $products->total() }} featured products found</p>
            </div>
        </div>
        
        <p class="text-gray-700 max-w-2xl">
            Discover our handpicked selection of premium products. These featured items represent the best in quality, 
            innovation, and customer satisfaction.
        </p>
    </div>

    <!-- Sorting & Filter Options -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between bg-gray-50 p-4 rounded-lg">
        <div class="flex items-center space-x-4 mb-4 sm:mb-0">
            <span class="text-sm font-medium text-gray-700">Sort by:</span>
            <select id="sortProducts" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
            </select>
        </div>
        
        <div class="flex items-center space-x-2 text-sm text-gray-600">
            <i class="fas fa-th-large"></i>
            <span>{{ $products->count() }} of {{ $products->total() }} products</span>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg border overflow-hidden hover:shadow-md transition-shadow group">
                <div class="relative bg-gray-100 aspect-square overflow-hidden">
                    <!-- Featured Badge -->
                    <div class="absolute top-2 left-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs px-2 py-1 rounded-full z-10 font-semibold flex items-center featured-badge">
                        <i class="fas fa-star text-xs mr-1"></i>
                        FEATURED
                    </div>
                    
                    <!-- Discount Badge -->
                    @if($product->discount_percentage > 0)
                    <div class="absolute top-2 left-2 mt-8 bg-red-500 text-white text-xs px-2 py-1 rounded z-10">
                        -{{ $product->discount_percentage }}%
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="absolute top-2 right-2 space-y-2 z-10">
                        <button onclick="toggleWishlist({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                            <i class="far fa-heart text-gray-600 hover:text-red-500" id="wishlist-{{ $product->id }}"></i>
                        </button>
                        <button onclick="quickView({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                            <i class="far fa-eye text-gray-600"></i>
                        </button>
                    </div>
                    
                    <!-- Product Image -->
                    <a href="{{ route('products.show', $product->slug) }}" class="block h-full p-4">
                        @if($product->primary_image)
                            <img src="{{ Storage::url($product->primary_image->image_path) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-400"></i>
                            </div>
                        @endif
                    </a>
                    
                    <!-- Add to Cart Button -->
                    @if($product->is_in_stock)
                    <button onclick="addToCart({{ $product->id }})" 
                            class="absolute bottom-0 left-0 right-0 bg-black text-white py-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        Add to Cart
                    </button>
                    @else
                    <div class="absolute bottom-0 left-0 right-0 bg-gray-500 text-white py-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center">
                        Out of Stock
                    </div>
                    @endif
                </div>
                
                <!-- Product Info -->
                <div class="p-4">
                    <div class="flex items-center mb-2">
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $product->category->name }}</span>
                        @if($product->brand)
                            <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded ml-2">{{ $product->brand }}</span>
                        @endif
                    </div>
                    
                    <h3 class="font-medium mb-2 line-clamp-2">
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:text-red-500">{{ $product->name }}</a>
                    </h3>
                    
                    <div class="flex items-center mb-2">
                        <div class="flex text-yellow-400 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= floor($product->average_rating ?? 4) ? '' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <span class="text-gray-500 text-sm ml-2">({{ $product->review_count ?? 0 }})</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-red-500 font-semibold">{{ $product->formatted_price }}</span>
                            @if($product->compare_price && $product->compare_price > $product->price)
                                <span class="text-gray-500 line-through text-sm">{{ $product->formatted_compare_price }}</span>
                            @endif
                        </div>
                        
                        <!-- Stock Status -->
                        <div class="flex items-center">
                            @if($product->is_in_stock)
                                <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>In Stock
                                </span>
                            @else
                                <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-full">
                                    <i class="fas fa-times-circle mr-1"></i>Out of Stock
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="w-24 h-24 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-4xl text-white"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No featured products yet</h3>
                <p class="text-gray-500 mb-4">We're working on featuring amazing products for you.</p>
                <a href="{{ route('products.index') }}" class="inline-block bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 transition-colors">
                    Browse All Products
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $products->appends(request()->query())->links() }}
    </div>
    @endif

    <!-- Featured Products Info -->
    <div class="mt-12 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-award text-white text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Premium Quality</h4>
                <p class="text-sm text-gray-600">Each featured product is carefully selected for exceptional quality and performance.</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Customer Favorites</h4>
                <p class="text-sm text-gray-600">These products are loved by our customers and have excellent reviews.</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shipping-fast text-white text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Fast Shipping</h4>
                <p class="text-sm text-gray-600">All featured products are in stock and ready for immediate shipping.</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick View Modal -->
<div id="quickViewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-bold">Quick View</h3>
                    <button onclick="closeQuickView()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <div id="quickViewContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Sort functionality
document.getElementById('sortProducts').addEventListener('change', function() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('sort', this.value);
    window.location.href = currentUrl.toString();
});

// Add to Cart
function addToCart(productId) {
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to cart!', 'success');
            updateCartCount();
        } else {
            showNotification(data.message || 'Error adding product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding product to cart', 'error');
    });
}

// Toggle Wishlist
function toggleWishlist(productId) {
    fetch(`/wishlist/toggle/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = document.getElementById(`wishlist-${productId}`);
            if (data.added) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-red-500');
                showNotification('Added to wishlist!', 'success');
            } else {
                icon.classList.remove('fas', 'text-red-500');
                icon.classList.add('far');
                showNotification('Removed from wishlist!', 'success');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Please login to add to wishlist', 'error');
    });
}

// Quick View
function quickView(productId) {
    fetch(`/products/${productId}/quick-view`)
    .then(response => response.text())
    .then(html => {
        document.getElementById('quickViewContent').innerHTML = html;
        document.getElementById('quickViewModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error loading product details', 'error');
    });
}

function closeQuickView() {
    document.getElementById('quickViewModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Update Cart Count
function updateCartCount() {
    fetch('/cart/count')
    .then(response => response.json())
    .then(data => {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = data.count;
            cartCount.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error updating cart count:', error);
    });
}

// Show Notification
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
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});
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

.aspect-square {
    aspect-ratio: 1 / 1;
}

/* Featured badge gradient animation */
@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.featured-badge {
    background: linear-gradient(-45deg, #fbbf24, #f59e0b, #d97706, #92400e);
    background-size: 400% 400%;
    animation: gradient-shift 3s ease infinite;
}

/* Hover effects */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}

/* Custom scrollbar for modal */
#quickViewModal .overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

#quickViewModal .overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#quickViewModal .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

#quickViewModal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endpush