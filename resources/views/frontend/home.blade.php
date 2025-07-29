@extends('layouts.frontend')

@section('title', 'Home - Blanzaa Electronics Store')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col lg:flex-row">
        <!-- Sidebar Categories -->
        <div class="lg:w-1/4 py-8 lg:border-r border-gray-200">
            <div class="space-y-4">
                @foreach($topCategories->take(9) as $category)
                <a href="{{ route('products.category', $category->slug) }}" 
                   class="flex justify-between items-center text-black hover:text-red-500 py-2 group transition-colors">
                    <span>{{ $category->name }}</span>
                    @if($category->products_count > 20)
                        <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                    @endif
                </a>
                @endforeach
            </div>
        </div>

        <!-- Main Hero Section -->
        <div class="lg:w-3/4 py-8">
            <div class="lg:ml-8">
                <div class="bg-black text-white rounded-lg p-8 lg:p-16 relative overflow-hidden min-h-[300px] lg:min-h-[400px]">
                    @php $heroProduct = $featuredProducts->first(); @endphp
                    <div class="relative z-10 max-w-md">
                        <div class="flex items-center mb-4">
                            <i class="fab fa-apple text-4xl mr-4"></i>
                            <span class="text-lg">{{ $heroProduct->category->name ?? 'Electronics' }}</span>
                        </div>
                        <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                            @if($heroProduct && $heroProduct->discount_percentage)
                                Up to {{ $heroProduct->discount_percentage }}%<br>
                                off Voucher
                            @else
                                Latest<br>
                                Collection
                            @endif
                        </h1>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center text-white border-b border-white pb-1 hover:text-gray-300 transition-colors">
                            Shop Now
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    
                    <!-- Product Image -->
                    <div class="absolute right-8 top-1/2 transform -translate-y-1/2 hidden lg:block">
                        @if($heroProduct && $heroProduct->image_url)
                            <img src="{{ $heroProduct->image_url }}" 
                                 alt="{{ $heroProduct->name }}" 
                                 class="w-64 h-80 object-contain">
                        @else
                            <div class="relative">
                                <div class="w-48 h-80 bg-gradient-to-br from-purple-400 via-pink-400 to-purple-500 rounded-[3rem] p-2 shadow-2xl">
                                    <div class="w-full h-full bg-black rounded-[2.5rem] flex items-center justify-center">
                                        <i class="fas fa-mobile-alt text-6xl text-white"></i>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Dots Indicator -->
                    <div class="absolute bottom-6 left-8 flex space-x-2">
                        @for($i = 0; $i < 5; $i++)
                            <div class="w-3 h-3 {{ $i === 2 ? 'bg-red-500' : 'bg-gray-500' }} rounded-full"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Flash Sales Section -->
@if($flashSaleProducts->count() > 0)
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex items-center mb-8">
        <div class="w-5 h-10 bg-red-500 rounded mr-4"></div>
        <span class="text-red-500 font-semibold">Today's</span>
    </div>
    
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 space-y-4 lg:space-y-0">
        <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-8">
            <h2 class="text-3xl font-bold mb-4 lg:mb-0">Flash Sales</h2>
            
            <!-- Countdown Timer -->
            <div class="flex items-center space-x-4">
                <div class="text-center">
                    <div class="text-2xl font-bold" id="days">03</div>
                    <div class="text-sm text-gray-500">Days</div>
                </div>
                <div class="text-red-500 text-2xl">:</div>
                <div class="text-center">
                    <div class="text-2xl font-bold" id="hours">23</div>
                    <div class="text-sm text-gray-500">Hours</div>
                </div>
                <div class="text-red-500 text-2xl">:</div>
                <div class="text-center">
                    <div class="text-2xl font-bold" id="minutes">19</div>
                    <div class="text-sm text-gray-500">Minutes</div>
                </div>
                <div class="text-red-500 text-2xl">:</div>
                <div class="text-center">
                    <div class="text-2xl font-bold" id="seconds">56</div>
                    <div class="text-sm text-gray-500">Seconds</div>
                </div>
            </div>
        </div>
        
        <div class="flex space-x-2">
            <button onclick="scrollProducts('flash-sales', -1)" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="scrollProducts('flash-sales', 1)" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Flash Sale Products -->
    <div id="flash-sales" class="overflow-hidden">
        <div class="flex space-x-6 transition-transform duration-500" id="flash-sales-container">
            @foreach($flashSaleProducts as $product)
            <div class="group cursor-pointer flex-shrink-0 w-64">
                <div class="relative bg-gray-100 rounded p-4 mb-4 aspect-square overflow-hidden">
                    <!-- Discount Badge -->
                    @if($product->discount_percentage)
                    <div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded z-10">
                        -{{ $product->discount_percentage }}%
                    </div>
                    @endif
                    
                    <!-- Featured Badge -->
                    @if($product->is_featured)
                    <div class="absolute top-2 left-2 {{ $product->discount_percentage ? 'mt-8' : '' }} bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs px-2 py-1 rounded-full z-10 font-semibold flex items-center featured-badge">
                        <i class="fas fa-star text-xs mr-1"></i>
                        FEATURED
                    </div>
                    @endif
                    
                    <!-- Wishlist & View Icons -->
                    <div class="absolute top-2 right-2 space-y-2 z-10">
                        <button onclick="toggleWishlist({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                            <i class="far fa-heart text-gray-600 hover:text-red-500" id="wishlist-{{ $product->id }}"></i>
                        </button>
                        <button onclick="quickView({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                            <i class="far fa-eye text-gray-600"></i>
                        </button>
                    </div>
                    
                    <!-- Product Image -->
                    <a href="{{ route('products.show', $product->slug) }}" class="flex items-center justify-center h-full">
                        <img src="{{ $product->thumbnail }}" 
                             alt="{{ $product->name }}" 
                             class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                    </a>
                    
                    <!-- Add to Cart Button -->
                    @if($product->is_in_stock)
                    <button onclick="addToCart({{ $product->id }})" 
                            class="absolute bottom-0 left-0 right-0 bg-black text-white py-2 rounded-b opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        Add To Cart
                    </button>
                    @else
                    <div class="absolute bottom-0 left-0 right-0 bg-gray-500 text-white py-2 rounded-b opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center">
                        Out of Stock
                    </div>
                    @endif
                </div>
                
                <div>
                    <h3 class="font-medium mb-1 line-clamp-2">{{ $product->name }}</h3>
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="text-red-500 font-medium">{{ $product->formatted_price }}</span>
                        @if($product->compare_price && $product->compare_price > $product->price)
                            <span class="text-gray-500 line-through text-sm">{{ $product->formatted_compare_price }}</span>
                        @endif
                    </div>
                    <div class="flex items-center">
                        <div class="flex text-yellow-400 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= floor($product->average_rating ?? 4) ? '' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <span class="text-gray-500 text-sm ml-1">({{ $product->review_count ?? 0 }})</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <div class="text-center mt-8">
        <a href="{{ route('products.index') }}" 
           class="inline-block bg-red-500 text-white px-8 py-3 rounded hover:bg-red-600 transition-colors font-medium">
            View All Products
        </a>
    </div>
</section>
@endif

<!-- Categories Section -->
<section class="max-w-7xl mx-auto px-4 py-16 border-t">
    <div class="flex items-center mb-8">
        <div class="w-5 h-10 bg-red-500 rounded mr-4"></div>
        <span class="text-red-500 font-semibold">Categories</span>
    </div>
    
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8">
        <h2 class="text-3xl font-bold mb-4 lg:mb-0">Browse By Category</h2>
        <div class="flex space-x-2">
            <button onclick="scrollProducts('categories', -1)" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="scrollProducts('categories', 1)" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        @php
        $categoryIcons = [
            'smartphones' => 'fas fa-mobile-alt',
            'laptops' => 'fas fa-laptop',
            'tablets' => 'fas fa-tablet-alt', 
            'headphones' => 'fas fa-headphones',
            'cameras' => 'fas fa-camera',
            'gaming' => 'fas fa-gamepad',
            'smartwatch' => 'fas fa-clock',
            'audio' => 'fas fa-volume-up',
            'accessories' => 'fas fa-plug'
        ];
        @endphp
        
        @foreach($topCategories->take(6) as $category)
        <a href="{{ route('products.category', $category->slug) }}" class="group" onclick="loadCategoryProducts({{ $category->id }})">
            <div class="border border-gray-300 rounded p-6 text-center hover:bg-red-500 hover:text-white hover:border-red-500 transition-all duration-300 cursor-pointer group min-h-[120px] flex flex-col justify-center">
                <i class="{{ $categoryIcons[strtolower($category->name)] ?? 'fas fa-cube' }} text-4xl mb-4 group-hover:text-white transition-colors"></i>
                <div class="font-medium">{{ $category->name }}</div>
                <div class="text-xs text-gray-500 group-hover:text-gray-200 mt-1">{{ $category->products_count }} products</div>
            </div>
        </a>
        @endforeach
    </div>
</section>

<!-- Featured Products Section (Menggantikan Best Selling) -->
@if($featuredProducts->count() > 0)
<section class="max-w-7xl mx-auto px-4 py-16 border-t">
    <div class="flex items-center mb-8">
        <div class="w-5 h-10 bg-red-500 rounded mr-4"></div>
        <span class="text-red-500 font-semibold">Featured</span>
    </div>
    
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8">
        <h2 class="text-3xl font-bold mb-4 lg:mb-0">Featured Products</h2>
        <a href="{{ route('products.featured') }}" 
           class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 transition-colors font-medium">
            View All
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($featuredProducts->take(4) as $product)
        <div class="group cursor-pointer">
            <div class="relative bg-gray-100 rounded p-4 mb-4 aspect-square overflow-hidden">
                <!-- Featured Badge -->
                <div class="absolute top-2 left-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs px-2 py-1 rounded-full z-10 font-semibold flex items-center featured-badge">
                    <i class="fas fa-star text-xs mr-1"></i>
                    FEATURED
                </div>
                
                <!-- Discount Badge (if applicable) -->
                @if($product->discount_percentage)
                <div class="absolute top-2 left-2 mt-8 bg-red-500 text-white text-xs px-2 py-1 rounded z-10">
                    -{{ $product->discount_percentage }}%
                </div>
                @endif
                
                <!-- Wishlist & View Icons -->
                <div class="absolute top-2 right-2 space-y-2 z-10">
                    <button onclick="toggleWishlist({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                        <i class="far fa-heart text-gray-600 hover:text-red-500" id="wishlist-{{ $product->id }}"></i>
                    </button>
                    <button onclick="quickView({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                        <i class="far fa-eye text-gray-600"></i>
                    </button>
                </div>
                
                <!-- Product Image -->
                <a href="{{ route('products.show', $product->slug) }}" class="flex items-center justify-center h-full">
                    <img src="{{ $product->thumbnail }}" 
                         alt="{{ $product->name }}" 
                         class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                </a>
                
                <!-- Add to Cart Button -->
                @if($product->is_in_stock)
                <button onclick="addToCart({{ $product->id }})" 
                        class="absolute bottom-0 left-0 right-0 bg-black text-white py-2 rounded-b opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    Add To Cart
                </button>
                @else
                <div class="absolute bottom-0 left-0 right-0 bg-gray-500 text-white py-2 rounded-b opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center">
                    Out of Stock
                </div>
                @endif
            </div>
            
            <div>
                <h3 class="font-medium mb-1 line-clamp-2">{{ $product->name }}</h3>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="text-red-500 font-medium">{{ $product->formatted_price }}</span>
                    @if($product->compare_price && $product->compare_price > $product->price)
                        <span class="text-gray-500 line-through text-sm">{{ $product->formatted_compare_price }}</span>
                    @endif
                </div>
                <div class="flex items-center">
                    <div class="flex text-yellow-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= floor($product->average_rating ?? 4) ? '' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <span class="text-gray-500 text-sm ml-1">({{ $product->review_count ?? 0 }})</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

<!-- Latest Products -->
@if($latestProducts->count() > 0)
<section class="max-w-7xl mx-auto px-4 py-16 border-t">
    <div class="flex items-center mb-8">
        <div class="w-5 h-10 bg-red-500 rounded mr-4"></div>
        <span class="text-red-500 font-semibold">Our Products</span>
    </div>
    
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8">
        <h2 class="text-3xl font-bold mb-4 lg:mb-0">Explore Our Products</h2>
        <div class="flex space-x-2">
            <button onclick="scrollProducts('latest', -1)" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="scrollProducts('latest', 1)" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($latestProducts->take(8) as $product)
        <div class="group cursor-pointer">
            <div class="relative bg-gray-100 rounded p-4 mb-4 aspect-square overflow-hidden">
                @if($loop->iteration <= 2)
                <div class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded z-10">
                    NEW
                </div>
                @endif
                
                <!-- Featured Badge -->
                @if($product->is_featured)
                <div class="absolute top-2 left-2 {{ $loop->iteration <= 2 ? 'mt-8' : '' }} bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs px-2 py-1 rounded-full z-10 font-semibold flex items-center featured-badge">
                    <i class="fas fa-star text-xs mr-1"></i>
                    FEATURED
                </div>
                @endif
                
                <!-- Wishlist & View Icons -->
                <div class="absolute top-2 right-2 space-y-2 z-10">
                    <button onclick="toggleWishlist({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                        <i class="far fa-heart text-gray-600 hover:text-red-500" id="wishlist-{{ $product->id }}"></i>
                    </button>
                    <button onclick="quickView({{ $product->id }})" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-gray-50 transition-colors">
                        <i class="far fa-eye text-gray-600"></i>
                    </button>
                </div>
                
                <!-- Product Image -->
                <a href="{{ route('products.show', $product->slug) }}" class="flex items-center justify-center h-full">
                    <img src="{{ $product->thumbnail }}" 
                         alt="{{ $product->name }}" 
                         class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                </a>
                
                <!-- Add to Cart Button -->
                @if($product->is_in_stock)
                <button onclick="addToCart({{ $product->id }})" 
                        class="absolute bottom-0 left-0 right-0 bg-black text-white py-2 rounded-b opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    Add To Cart
                </button>
                @else
                <div class="absolute bottom-0 left-0 right-0 bg-gray-500 text-white py-2 rounded-b opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-center">
                    Out of Stock
                </div>
                @endif
            </div>
            
            <div>
                <h3 class="font-medium mb-1 line-clamp-2">{{ $product->name }}</h3>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="text-red-500 font-medium">{{ $product->formatted_price }}</span>
                    @if($product->compare_price && $product->compare_price > $product->price)
                        <span class="text-gray-500 line-through text-sm">{{ $product->formatted_compare_price }}</span>
                    @endif
                </div>
                <div class="flex items-center">
                    <div class="flex text-yellow-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= floor($product->average_rating ?? 4) ? '' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <span class="text-gray-500 text-sm ml-1">({{ $product->review_count ?? 0 }})</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="text-center">
        <a href="{{ route('products.index') }}" 
           class="inline-block bg-red-500 text-white px-8 py-3 rounded hover:bg-red-600 transition-colors font-medium">
            View All Products
        </a>
    </div>
</section>
@endif

<!-- Enhanced Music Experience Banner -->
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="bg-black text-white rounded-lg p-8 lg:p-16 relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
                <p class="text-green-400 font-semibold mb-4">Categories</p>
                <h2 class="text-4xl lg:text-5xl font-bold mb-6">Enhance Your Music Experience</h2>
                
                <!-- Countdown Timer -->
                <div class="flex space-x-4 mb-8">
                    <div class="bg-white text-black rounded-full w-16 h-16 flex flex-col items-center justify-center">
                        <div class="text-sm font-bold" id="music-days">23</div>
                        <div class="text-xs">Days</div>
                    </div>
                    <div class="bg-white text-black rounded-full w-16 h-16 flex flex-col items-center justify-center">
                        <div class="text-sm font-bold" id="music-hours">05</div>
                        <div class="text-xs">Hours</div>
                    </div>
                    <div class="bg-white text-black rounded-full w-16 h-16 flex flex-col items-center justify-center">
                        <div class="text-sm font-bold" id="music-minutes">59</div>
                        <div class="text-xs">Minutes</div>
                    </div>
                    <div class="bg-white text-black rounded-full w-16 h-16 flex flex-col items-center justify-center">
                        <div class="text-sm font-bold" id="music-seconds">35</div>
                        <div class="text-xs">Seconds</div>
                    </div>
                </div>
                
                @php
                    // Cari kategori audio/headphones atau produk audio featured
                    $audioCategory = $topCategories->firstWhere('slug', 'audio') 
                                  ?? $topCategories->firstWhere('slug', 'headphones')
                                  ?? $topCategories->firstWhere('name', 'Audio')
                                  ?? $topCategories->firstWhere('name', 'Headphones');
                    
                    // Jika tidak ada kategori audio, gunakan produk featured yang berhubungan dengan audio
                    $audioProduct = $featuredProducts->filter(function($product) {
                        return stripos($product->name, 'headphone') !== false 
                            || stripos($product->name, 'speaker') !== false
                            || stripos($product->name, 'audio') !== false
                            || stripos($product->category->name ?? '', 'audio') !== false
                            || stripos($product->category->name ?? '', 'headphone') !== false;
                    })->first();
                @endphp
                
                @if($audioCategory)
                    <a href="{{ route('products.category', $audioCategory->slug) }}" 
                       class="inline-block bg-green-500 text-white px-8 py-3 rounded hover:bg-green-600 transition-colors font-medium">
                        Buy Now!
                    </a>

                    @elseif($audioProduct)
                    <a href="{{ route('products.show', $audioProduct->slug) }}" 
                       class="inline-block bg-green-500 text-white px-8 py-3 rounded hover:bg-green-600 transition-colors font-medium">
                        Buy Now!
                    </a>
                @else
                    <a href="{{ route('products.index', ['search' => 'audio']) }}" 
                       class="inline-block bg-green-500 text-white px-8 py-3 rounded hover:bg-green-600 transition-colors font-medium">
                        Buy Now!
                    </a>
                @endif
            </div>
            
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-gray-400 to-gray-600 rounded-full opacity-20 blur-3xl"></div>
                
                @if($audioProduct && $audioProduct->image_url)
                    <img src="{{ $audioProduct->image_url }}" 
                         alt="{{ $audioProduct->name }}" 
                         class="relative z-10 w-full max-w-md mx-auto object-contain">
                @else
                    <!-- Default Audio Product Image atau Placeholder -->
                    <div class="relative z-10 w-full max-w-md mx-auto">
                        <div class="w-64 h-64 bg-gradient-to-br from-green-400 to-blue-500 rounded-full mx-auto flex items-center justify-center shadow-2xl">
                            <i class="fas fa-volume-up text-6xl text-white"></i>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="max-w-7xl mx-auto px-4 py-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center">
            <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-truck text-2xl text-gray-600"></i>
            </div>
            <h3 class="font-bold text-lg mb-2">FREE AND FAST DELIVERY</h3>
            <p class="text-gray-600">Free delivery for all orders over $140</p>
        </div>
        
        <div class="text-center">
            <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-headset text-2xl text-gray-600"></i>
            </div>
            <h3 class="font-bold text-lg mb-2">24/7 CUSTOMER SERVICE</h3>
            <p class="text-gray-600">Friendly 24/7 customer support</p>
        </div>
        
        <div class="text-center">
            <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-2xl text-gray-600"></i>
            </div>
            <h3 class="font-bold text-lg mb-2">MONEY BACK GUARANTEE</h3>
            <p class="text-gray-600">We return money within 30 days</p>
        </div>
    </div>
</section>

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
// Enhanced Countdown Timer
function updateCountdown() {
    const now = new Date().getTime();
    const endTime = now + (3 * 24 * 60 * 60 * 1000); // 3 days from now
    
    const distance = endTime - now;
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    // Update flash sales countdown
    const flashDays = document.getElementById('days');
    const flashHours = document.getElementById('hours');
    const flashMinutes = document.getElementById('minutes');
    const flashSeconds = document.getElementById('seconds');
    
    if (flashDays) flashDays.textContent = days.toString().padStart(2, '0');
    if (flashHours) flashHours.textContent = hours.toString().padStart(2, '0');
    if (flashMinutes) flashMinutes.textContent = minutes.toString().padStart(2, '0');
    if (flashSeconds) flashSeconds.textContent = seconds.toString().padStart(2, '0');
}

// Enhanced Music Experience Countdown Timer
function updateMusicCountdown() {
    // Set target date: 30 days from now
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + 30);
    
    const now = new Date().getTime();
    const distance = targetDate.getTime() - now;
    
    if (distance > 0) {
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Update music banner countdown
        const musicDays = document.getElementById('music-days');
        const musicHours = document.getElementById('music-hours');
        const musicMinutes = document.getElementById('music-minutes');
        const musicSeconds = document.getElementById('music-seconds');
        
        if (musicDays) musicDays.textContent = days.toString().padStart(2, '0');
        if (musicHours) musicHours.textContent = hours.toString().padStart(2, '0');
        if (musicMinutes) musicMinutes.textContent = minutes.toString().padStart(2, '0');
        if (musicSeconds) musicSeconds.textContent = seconds.toString().padStart(2, '0');
    }
}

// Update countdowns every second
setInterval(updateCountdown, 1000);
setInterval(updateMusicCountdown, 1000);
updateCountdown();
updateMusicCountdown();

// Product Scrolling
let scrollPositions = {};

function scrollProducts(containerId, direction) {
    const container = document.getElementById(containerId + '-container');
    if (!container) return;
    
    const scrollAmount = 300;
    const currentScroll = scrollPositions[containerId] || 0;
    const newScroll = currentScroll + (direction * scrollAmount);
    
    scrollPositions[containerId] = Math.max(0, newScroll);
    container.style.transform = `translateX(-${scrollPositions[containerId]}px)`;
}

// Add to Cart
function addToCart(productId) {
    fetch(`/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount();
            
            // Show success message
            showNotification('Product added to cart!', 'success');
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

// Load Category Products
function loadCategoryProducts(categoryId) {
    // This can be used for dynamic loading if needed
    console.log('Loading category products:', categoryId);
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

// Newsletter Subscription
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('form');
    if (newsletterForm && newsletterForm.querySelector('input[type="email"]')) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            fetch('/newsletter/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Successfully subscribed to newsletter!', 'success');
                    this.querySelector('input[type="email"]').value = '';
                } else {
                    showNotification(data.message || 'Error subscribing to newsletter', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error subscribing to newsletter', 'error');
            });
        });
    }
});

// Initialize on page load
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

/* Pulse animation for music countdown */
#music-days, #music-hours, #music-minutes, #music-seconds {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Smooth scrolling for product containers */
#flash-sales-container,
#categories-container,
#latest-container {
    transition: transform 0.3s ease;
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

/* Hover effects for product cards */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}

/* Category icons hover animation */
.group:hover i {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Loading animation for images */
img {
    transition: opacity 0.3s ease;
}

img[src=""] {
    opacity: 0;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .grid-cols-2 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .lg\:text-6xl {
        font-size: 2.5rem;
    }
    
    .lg\:text-5xl {
        font-size: 2rem;
    }
    
    .lg\:p-16 {
        padding: 2rem;
    }
    
    .lg\:space-x-8 {
        margin-left: 0;
    }
    
    .lg\:space-x-8 > * + * {
        margin-left: 0;
        margin-top: 1rem;
    }
}

@media (max-width: 640px) {
    .sm\:grid-cols-2 {
        grid-template-columns: repeat(1, 1fr);
    }
    
    .flex-shrink-0.w-64 {
        width: 16rem;
    }
    
    .aspect-square {
        aspect-ratio: 1 / 1;
    }
}

/* Animation for countdown numbers */
@keyframes countdownPulse {
    0%, 100% { 
        transform: scale(1);
        color: inherit;
    }
    50% { 
        transform: scale(1.1);
        color: #ef4444;
    }
}

.countdown-number {
    animation: countdownPulse 1s ease-in-out infinite;
}

/* Shimmer effect for loading */
@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: calc(200px + 100%) 0;
    }
}

.shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}
</style>
@endpush