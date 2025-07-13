@extends('layouts.frontend')

@section('title', $product->name . ' - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('products.index') }}" class="hover:text-red-500">Products</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('products.category', $product->category->slug) }}" class="hover:text-red-500">{{ $product->category->name }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Images -->
        <div class="space-y-4">
            <!-- Main Image -->
            <div class="bg-gray-100 rounded-lg overflow-hidden aspect-square">
                @if($product->images->count() > 0)
                    <img id="main-image" src="{{ Storage::url($product->images->first()->image_path) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-contain">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-image text-6xl text-gray-400"></i>
                    </div>
                @endif
            </div>
            
            <!-- Thumbnail Images -->
            @if($product->images->count() > 1)
            <div class="grid grid-cols-4 gap-2">
                @foreach($product->images as $image)
                <button class="bg-gray-100 rounded overflow-hidden aspect-square hover:ring-2 hover:ring-red-500 transition-all thumbnail-btn"
                        onclick="changeMainImage('{{ Storage::url($image->image_path) }}')">
                    <img src="{{ Storage::url($image->image_path) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-contain">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
            <!-- Title and Rating -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex items-center">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= floor($product->average_rating ?? 0) ? '' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <span class="text-gray-600 ml-2">({{ $product->review_count ?? 0 }} reviews)</span>
                    </div>
                    <span class="text-sm text-gray-500">SKU: {{ $product->sku }}</span>
                </div>
            </div>

            <!-- Price -->
            <div class="space-y-2">
                <div class="flex items-center space-x-4">
                    <span class="text-3xl font-bold text-red-500">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                    @if($product->compare_price && $product->compare_price > $product->price)
                        <span class="text-xl text-gray-500 line-through">
                            Rp {{ number_format($product->compare_price, 0, ',', '.') }}
                        </span>
                        <span class="bg-red-100 text-red-800 text-sm px-2 py-1 rounded">
                            Save {{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-600">Tax included. Shipping calculated at checkout.</p>
            </div>

            <!-- Stock Status -->
            <div class="flex items-center space-x-2">
                @if($product->stock_quantity > 0)
                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                    <span class="text-green-600 font-medium">
                        In Stock ({{ $product->stock_quantity }} available)
                    </span>
                @else
                    <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                    <span class="text-red-600 font-medium">Out of Stock</span>
                @endif
            </div>

            <!-- Short Description -->
            @if($product->short_description)
            <div class="prose prose-sm max-w-none">
                <p class="text-gray-700">{{ $product->short_description }}</p>
            </div>
            @endif

            <!-- Quantity and Add to Cart -->
            @if($product->stock_quantity > 0)
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Quantity:</label>
                    <div class="flex items-center border border-gray-300 rounded">
                        <button type="button" onclick="decreaseQuantity()" 
                                class="px-3 py-2 hover:bg-gray-100 transition-colors">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" 
                               class="w-16 px-3 py-2 text-center border-0 focus:ring-0">
                        <button type="button" onclick="increaseQuantity()" 
                                class="px-3 py-2 hover:bg-gray-100 transition-colors">+</button>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button onclick="addToCart({{ $product->id }})" 
                            class="flex-1 bg-red-500 text-white py-3 px-6 rounded-lg hover:bg-red-600 transition-colors font-medium">
                        <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                    </button>
                    @auth
                        <a href="{{ route('checkout.index') }}" 
                        onclick="addToCartAndRedirect({{ $product->id }}); return false;"
                        class="flex-1 bg-gray-900 text-white py-3 px-6 rounded-lg hover:bg-gray-800 transition-colors font-medium text-center">
                            Buy Now
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                        class="flex-1 bg-gray-900 text-white py-3 px-6 rounded-lg hover:bg-gray-800 transition-colors font-medium text-center">
                            Buy Now
                        </a>
                    @endauth
                </div>
            </div>
            @else
            <div class="space-y-4">
                <button disabled class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg cursor-not-allowed font-medium">
                    Out of Stock
                </button>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <p class="text-sm text-gray-600">This product is currently out of stock. Please check back later or contact us for availability.</p>
                </div>
            </div>
            @endif

            <!-- Product Features -->
            @if($product->features)
            <div class="space-y-2">
                <h3 class="text-lg font-semibold text-gray-900">Key Features</h3>
                <ul class="space-y-1">
                    @foreach(json_decode($product->features, true) ?? [] as $feature)
                    <li class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>{{ $feature }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Shipping Info -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-2">
                <div class="flex items-center text-sm">
                    <i class="fas fa-shipping-fast text-gray-600 mr-2"></i>
                    <span class="font-medium">Free shipping</span>
                    <span class="text-gray-600 ml-1">on orders over Rp 1.000.000</span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-undo text-gray-600 mr-2"></i>
                    <span class="font-medium">30-day returns</span>
                    <span class="text-gray-600 ml-1">Easy returns and exchanges</span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-shield-alt text-gray-600 mr-2"></i>
                    <span class="font-medium">Warranty</span>
                    <span class="text-gray-600 ml-1">{{ $product->warranty ?? '1 year' }} manufacturer warranty</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="mb-12">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button class="py-2 px-1 border-b-2 border-red-500 text-red-600 font-medium text-sm tab-btn active" 
                        onclick="showTab('description')">
                    Description
                </button>
                <button class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm tab-btn" 
                        onclick="showTab('specifications')">
                    Specifications
                </button>
                <button class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm tab-btn" 
                        onclick="showTab('reviews')">
                    Reviews ({{ $product->review_count ?? 0 }})
                </button>
                <button class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm tab-btn" 
                        onclick="showTab('shipping')">
                    Shipping
                </button>
            </nav>
        </div>

        <div class="mt-8">
            <!-- Description Tab -->
            <div id="description-tab" class="tab-content">
                <div class="prose max-w-none">
                    {!! $product->description ?: '<p>No description available.</p>' !!}
                </div>
            </div>

            <!-- Specifications Tab -->
            <div id="specifications-tab" class="tab-content hidden">
                @if($product->specifications)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(json_decode($product->specifications, true) ?? [] as $key => $value)
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                            <span class="text-gray-600">{{ $value }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No specifications available.</p>
                @endif
            </div>

            <!-- Reviews Tab -->
            <div id="reviews-tab" class="tab-content hidden">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Customer Reviews</h3>
                        <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                            Write a Review
                        </button>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-gray-900 mb-2">
                                {{ number_format($product->average_rating ?? 0, 1) }}
                            </div>
                            <div class="flex items-center justify-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-yellow-400 {{ $i <= floor($product->average_rating ?? 0) ? '' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600">Based on {{ $product->review_count ?? 0 }} reviews</p>
                        </div>
                    </div>

                    <!-- Review List would go here -->
                    <div class="space-y-4">
                        <p class="text-gray-500 text-center py-8">No reviews yet. Be the first to review this product!</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Tab -->
            <div id="shipping-tab" class="tab-content hidden">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Shipping Options</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 border border-gray-200 rounded">
                                    <div>
                                        <p class="font-medium">Standard Shipping</p>
                                        <p class="text-sm text-gray-600">5-7 business days</p>
                                    </div>
                                    <span class="text-gray-900">Rp 25.000</span>
                                </div>
                                <div class="flex justify-between items-center p-3 border border-gray-200 rounded">
                                    <div>
                                        <p class="font-medium">Express Shipping</p>
                                        <p class="text-sm text-gray-600">2-3 business days</p>
                                    </div>
                                    <span class="text-gray-900">Rp 50.000</span>
                                </div>
                                <div class="flex justify-between items-center p-3 border border-green-200 rounded bg-green-50">
                                    <div>
                                        <p class="font-medium text-green-800">Free Shipping</p>
                                        <p class="text-sm text-green-600">On orders over Rp 1.000.000</p>
                                    </div>
                                    <span class="text-green-800">Free</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Return Policy</h3>
                            <div class="space-y-3 text-sm text-gray-600">
                                <p>• 30-day return window from delivery date</p>
                                <p>• Items must be unused and in original packaging</p>
                                <p>• Return shipping costs may apply</p>
                                <p>• Refunds processed within 5-7 business days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Related Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-lg border overflow-hidden hover:shadow-md transition-shadow group">
                <div class="relative bg-gray-100 aspect-square overflow-hidden">
                    <a href="{{ route('products.show', $relatedProduct->slug) }}" class="block h-full p-4">
                        @if($relatedProduct->images->count() > 0)
                            <img src="{{ Storage::url($relatedProduct->images->first()->image_path) }}" 
                                 alt="{{ $relatedProduct->name }}" 
                                 class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-400"></i>
                            </div>
                        @endif
                    </a>
                </div>
                
                <div class="p-4">
                    <h3 class="font-medium mb-2 line-clamp-2">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="hover:text-red-500">{{ $relatedProduct->name }}</a>
                    </h3>
                    
                    <div class="flex items-center space-x-2">
                        <span class="text-red-500 font-semibold">
                            Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Product image functionality
function changeMainImage(imageSrc) {
    document.getElementById('main-image').src = imageSrc;
    
    // Update thumbnail active state
    document.querySelectorAll('.thumbnail-btn').forEach(btn => {
        btn.classList.remove('ring-2', 'ring-red-500');
    });
    event.currentTarget.classList.add('ring-2', 'ring-red-500');
}

// Quantity functionality
function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const max = parseInt(quantityInput.getAttribute('max'));
    const current = parseInt(quantityInput.value);
    
    if (current < max) {
        quantityInput.value = current + 1;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const min = parseInt(quantityInput.getAttribute('min'));
    const current = parseInt(quantityInput.value);
    
    if (current > min) {
        quantityInput.value = current - 1;
    }
}

// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active state from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-red-500', 'text-red-600', 'active');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active state to selected tab button
    event.currentTarget.classList.remove('border-transparent', 'text-gray-500');
    event.currentTarget.classList.add('border-red-500', 'text-red-600', 'active');
}

// Check if user is logged in (menggunakan fungsi dari frontend.blade.php)
function isUserLoggedIn() {
    const authMeta = document.querySelector('meta[name="user-authenticated"]');
    return authMeta && authMeta.getAttribute('content') === 'true';
}

// FIXED: Cart functionality dengan login check
function addToCart(productId) {
    // Check login status first
    if (!isUserLoggedIn()) {
        // Langsung redirect ke login
        window.location.href = '/login';
        return;
    }

    const quantity = document.getElementById('quantity').value;
    
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Menggunakan fungsi global dari frontend.blade.php
            if (typeof window.showNotification === 'function') {
                window.showNotification('Product added to cart!', 'success');
            }
            if (typeof window.updateCartCount === 'function') {
                window.updateCartCount();
            }
        } else {
            if (typeof window.showNotification === 'function') {
                window.showNotification(data.message || 'Error adding product to cart', 'error');
            }
        }
    })
    .catch(error => {
        if (typeof window.showNotification === 'function') {
            window.showNotification('Error adding product to cart', 'error');
        }
    });
}

// Buy Now functionality
function addToCartAndRedirect(productId) {
    // Check login status first
    if (!isUserLoggedIn()) {
        window.location.href = '/login';
        return;
    }

    const quantity = document.getElementById('quantity').value;
    
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/checkout';
        } else {
            if (typeof window.showNotification === 'function') {
                window.showNotification(data.message || 'Error adding product to cart', 'error');
            }
        }
    })
    .catch(error => {
        if (typeof window.showNotification === 'function') {
            window.showNotification('Error processing request', 'error');
        }
    });
}

// Wishlist functionality
function toggleWishlist(productId) {
    if (!isUserLoggedIn()) {
        window.location.href = '/login';
        return;
    }

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
            if (icon) {
                if (data.added) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-red-500');
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('Added to wishlist!', 'success');
                    }
                } else {
                    icon.classList.remove('fas', 'text-red-500');
                    icon.classList.add('far');
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('Removed from wishlist!', 'success');
                    }
                }
            }
        }
    })
    .catch(error => {
        if (typeof window.showNotification === 'function') {
            window.showNotification('Error updating wishlist', 'error');
        }
    });
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

.prose {
    max-width: none;
}

.prose p {
    margin-bottom: 1rem;
}

.prose ul {
    margin: 1rem 0;
    padding-left: 1.5rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

/* Custom input number styling */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}
</style>
@endpush