@extends('layouts.frontend')

@section('title', 'Search Results for "' . $query . '" - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('products.index') }}" class="hover:text-red-500">Products</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Search Results</li>
        </ol>
    </nav>

    <!-- Search Results Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <i class="fas fa-search text-4xl text-red-500 mr-4"></i>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Search Results for "{{ $query }}"</h1>
                <p class="text-gray-600">{{ $products->total() }} products found</p>
            </div>
        </div>
        
        <p class="text-gray-700 max-w-2xl">Showing search results for your query</p>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg border overflow-hidden hover:shadow-md transition-shadow group">
                <div class="relative bg-gray-100 aspect-square overflow-hidden">
                    @if($product->discount_percentage > 0)
                    <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded z-10">
                        -{{ $product->discount_percentage }}%
                    </div>
                    @endif
                    
                    <div class="absolute top-2 right-2 space-y-2 z-10">
                        <button onclick="toggleWishlist({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                            <i class="far fa-heart text-gray-600 hover:text-red-500" id="wishlist-{{ $product->id }}"></i>
                        </button>
                        <button onclick="quickView({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50">
                            <i class="far fa-eye text-gray-600"></i>
                        </button>
                    </div>
                    
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
                
                <div class="p-4">
                    <h3 class="font-medium mb-2 line-clamp-2">
                        <a href="{{ route('products.show', $product->slug) }}" class="hover:text-red-500">{{ $product->name }}</a>
                    </h3>
                    
                    <div class="flex items-center mb-2">
                        <div class="flex text-yellow-400 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= floor($product->average_rating ?? 0) ? '' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <span class="text-gray-500 text-sm ml-2">({{ $product->review_count ?? 0 }})</span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <span class="text-red-500 font-semibold">{{ $product->formatted_price }}</span>
                        @if($product->compare_price && $product->compare_price > $product->price)
                            <span class="text-gray-500 line-through text-sm">{{ $product->formatted_compare_price }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-search text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
                <p class="text-gray-500 mb-4">We couldn't find any products matching your search for "{{ $query }}".</p>
                
                <div class="space-y-2 mb-6">
                    <p class="text-sm text-gray-600">Try:</p>
                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Checking your spelling</li>
                        <li>Using different keywords</li>
                        <li>Using more general terms</li>
                        <li>Browsing our categories</li>
                    </ul>
                </div>
                
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors">
                    Browse All Products
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Reuse functions from other pages
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
        } else {
            showNotification(data.message || 'Error adding product to cart', 'error');
        }
    })
    .catch(error => {
        showNotification('Error adding product to cart', 'error');
    });
}

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
        showNotification('Please login to add to wishlist', 'error');
    });
}

function quickView(productId) {
    fetch(`/products/${productId}/quick-view`)
    .then(response => response.text())
    .then(html => {
        document.getElementById('quickViewContent').innerHTML = html;
        document.getElementById('quickViewModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    })
    .catch(error => {
        showNotification('Error loading product details', 'error');
    });
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
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.aspect-square {
    aspect-ratio: 1 / 1;
}
</style>
@endpush