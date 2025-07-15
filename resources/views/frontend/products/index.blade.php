@extends('layouts.frontend')

@section('title', 'All Products - Exclusive Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-red-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900">Products</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg border p-6 sticky top-4">
                <h3 class="text-lg font-semibold mb-4">Filters</h3>
                
                <!-- Category Filter -->
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Category</h4>
                    <div class="space-y-2">
                        @php
                            $selectedCategories = request('categories');
                            if (is_string($selectedCategories)) {
                                $selectedCategories = explode(',', $selectedCategories);
                            } elseif (!is_array($selectedCategories)) {
                                $selectedCategories = [];
                            }
                        @endphp
                        @foreach($categories as $category)
                        <label class="flex items-center">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                   class="rounded border-gray-300 text-red-500 focus:ring-red-500"
                                   {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">{{ $category->name }}</span>
                            <span class="ml-auto text-xs text-gray-500">({{ $category->products_count }})</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Price Range</h4>
                    <div class="space-y-2">
                        @php
                            $selectedPriceRange = request('price_range');
                        @endphp
                        <label class="flex items-center">
                            <input type="radio" name="price_range" value="0-500000" 
                                   class="text-red-500 focus:ring-red-500"
                                   {{ $selectedPriceRange == '0-500000' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">Under Rp 500.000</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="price_range" value="500000-1000000" 
                                   class="text-red-500 focus:ring-red-500"
                                   {{ $selectedPriceRange == '500000-1000000' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">Rp 500.000 - Rp 1.000.000</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="price_range" value="1000000-2000000" 
                                   class="text-red-500 focus:ring-red-500"
                                   {{ $selectedPriceRange == '1000000-2000000' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">Rp 1.000.000 - Rp 2.000.000</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="price_range" value="2000000-999999999" 
                                   class="text-red-500 focus:ring-red-500"
                                   {{ $selectedPriceRange == '2000000-999999999' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">Above Rp 2.000.000</span>
                        </label>
                    </div>
                </div>

                <!-- Brand Filter -->
                @if($brands->count() > 0)
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Brand</h4>
                    <div class="space-y-2">
                        @php
                            $selectedBrands = request('brands');
                            if (is_string($selectedBrands)) {
                                $selectedBrands = explode(',', $selectedBrands);
                            } elseif (!is_array($selectedBrands)) {
                                $selectedBrands = [];
                            }
                        @endphp
                        @foreach($brands as $brand)
                        <label class="flex items-center">
                            <input type="checkbox" name="brands[]" value="{{ $brand }}" 
                                   class="rounded border-gray-300 text-red-500 focus:ring-red-500"
                                   {{ in_array($brand, $selectedBrands) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">{{ $brand }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Rating Filter -->
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Rating</h4>
                    <div class="space-y-2">
                        @php
                            $selectedRating = request('rating');
                        @endphp
                        @for($i = 5; $i >= 1; $i--)
                        <label class="flex items-center">
                            <input type="radio" name="rating" value="{{ $i }}" 
                                   class="text-red-500 focus:ring-red-500"
                                   {{ $selectedRating == $i ? 'checked' : '' }}>
                            <div class="ml-2 flex items-center">
                                @for($j = 1; $j <= 5; $j++)
                                    <i class="fas fa-star text-sm {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="ml-1 text-sm">& up</span>
                            </div>
                        </label>
                        @endfor
                    </div>
                </div>

                <!-- Availability Filter -->
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Availability</h4>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="in_stock" value="1" 
                                   class="rounded border-gray-300 text-red-500 focus:ring-red-500"
                                   {{ request('in_stock') == '1' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">In Stock</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="on_sale" value="1" 
                                   class="rounded border-gray-300 text-red-500 focus:ring-red-500"
                                   {{ request('on_sale') == '1' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">On Sale</span>
                        </label>
                    </div>
                </div>

                <!-- Clear Filters Button -->
                <button onclick="clearFilters()" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 transition-colors">
                    Clear All Filters
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:w-3/4">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold mb-2">All Products</h1>
                    <p class="text-gray-600">Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} results</p>
                </div>
                
                <!-- Sort Options -->
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Sort by:</span>
                        <select id="sort-select" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        </select>
                    </div>
                    
                    <!-- View Toggle -->
                    <div class="flex items-center space-x-1">
                        <button id="grid-view" class="p-2 rounded {{ request('view') != 'list' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600' }}">
                            <i class="fas fa-th"></i>
                        </button>
                        <button id="list-view" class="p-2 rounded {{ request('view') == 'list' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600' }}">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="products-container" class="{{ request('view') == 'list' ? 'space-y-4' : 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6' }}">
                @forelse($products as $product)
                    @if(request('view') == 'list')
                        <!-- List View -->
                        <div class="bg-white rounded-lg border p-4 flex space-x-4 hover:shadow-md transition-shadow">
                            <div class="flex-shrink-0 w-32 h-32 bg-gray-100 rounded overflow-hidden">
                                @if($product->primary_image)
                                    <img src="{{ Storage::url($product->primary_image->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-2xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg mb-2">
                                    <a href="{{ route('products.show', $product->slug) }}" class="hover:text-red-500">{{ $product->name }}</a>
                                </h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->short_description }}</p>
                                
                                <div class="flex items-center mb-2">
                                    <div class="flex text-yellow-400 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= floor($product->average_rating ?? 0) ? '' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-gray-500 text-sm ml-2">({{ $product->review_count ?? 0 }})</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-red-500 font-semibold text-lg">{{ $product->formatted_price }}</span>
                                        @if($product->compare_price && $product->compare_price > $product->price)
                                            <span class="text-gray-500 line-through text-sm">{{ $product->formatted_compare_price }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <!-- CHANGED: Lihat Detail Button -->
                                        <a href="{{ route('products.show', $product->slug) }}" 
                                           class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors text-sm">
                                            <i class="fas fa-eye mr-1"></i>Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Grid View -->
                        <div class="bg-white rounded-lg border overflow-hidden hover:shadow-md transition-shadow group">
                            <div class="relative bg-gray-100 aspect-square overflow-hidden">
                                @if($product->discount_percentage > 0)
                                <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded z-10">
                                    -{{ $product->discount_percentage }}%
                                </div>
                                @endif
                                
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
                                
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-red-500 font-semibold">{{ $product->formatted_price }}</span>
                                        @if($product->compare_price && $product->compare_price > $product->price)
                                            <span class="text-gray-500 line-through text-sm">{{ $product->formatted_compare_price }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- CHANGED: Full width Lihat Detail Button -->
                                <a href="{{ route('products.show', $product->slug) }}" 
                                   class="w-full bg-red-500 text-white px-3 py-2 rounded text-sm hover:bg-red-600 transition-colors text-center block">
                                    <i class="fas fa-eye mr-1"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
                        <p class="text-gray-500">Try adjusting your filters or search terms.</p>
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
    </div>
</div>

@endsection

@push('scripts')
<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Sort change handler
    document.getElementById('sort-select').addEventListener('change', function() {
        updateFilters();
    });

    // View toggle handlers
    document.getElementById('grid-view').addEventListener('click', function() {
        changeView('grid');
    });

    document.getElementById('list-view').addEventListener('click', function() {
        changeView('list');
    });

    // Filter change handlers
    document.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(function(input) {
        input.addEventListener('change', function() {
            updateFilters();
        });
    });
});

function updateFilters() {
    const url = new URL(window.location.href);
    
    // Get all filter values
    const categories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked')).map(el => el.value);
    const brands = Array.from(document.querySelectorAll('input[name="brands[]"]:checked')).map(el => el.value);
    const priceRange = document.querySelector('input[name="price_range"]:checked')?.value;
    const rating = document.querySelector('input[name="rating"]:checked')?.value;
    const inStock = document.querySelector('input[name="in_stock"]:checked')?.value;
    const onSale = document.querySelector('input[name="on_sale"]:checked')?.value;
    const sort = document.getElementById('sort-select').value;

    // Clear existing parameters
    url.searchParams.delete('categories');
    url.searchParams.delete('brands');
    url.searchParams.delete('price_range');
    url.searchParams.delete('rating');
    url.searchParams.delete('in_stock');
    url.searchParams.delete('on_sale');
    url.searchParams.delete('sort');

    // Set new parameters
    if (categories.length) url.searchParams.set('categories', categories.join(','));
    if (brands.length) url.searchParams.set('brands', brands.join(','));
    if (priceRange) url.searchParams.set('price_range', priceRange);
    if (rating) url.searchParams.set('rating', rating);
    if (inStock) url.searchParams.set('in_stock', inStock);
    if (onSale) url.searchParams.set('on_sale', onSale);
    if (sort && sort !== 'latest') url.searchParams.set('sort', sort);

    window.location.href = url.toString();
}

function changeView(view) {
    const url = new URL(window.location.href);
    if (view === 'list') {
        url.searchParams.set('view', view);
    } else {
        url.searchParams.delete('view');
    }
    window.location.href = url.toString();
}

function clearFilters() {
    const url = new URL(window.location.href);
    // Keep only the base URL
    url.search = '';
    window.location.href = url.toString();
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

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endpush