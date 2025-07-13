<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Get categories for filter
        $categories = Category::withCount('products')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get brands for filter (if Brand model exists)
        $brands = collect();
        if (class_exists('\App\Models\Brand')) {
            $brands = Brand::where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        // Build query
        $query = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('categories')) {
            $categoryIds = explode(',', $request->categories);
            $query->whereIn('category_id', $categoryIds);
        }

        if ($request->filled('brands')) {
            $brandIds = explode(',', $request->brands);
            $query->whereIn('brand_id', $brandIds);
        }

        if ($request->filled('price_range')) {
            $priceRange = explode('-', $request->price_range);
            if (count($priceRange) == 2) {
                $query->whereBetween('price', [$priceRange[0], $priceRange[1]]);
            }
        }

        if ($request->filled('rating')) {
            // This would need a proper rating system
            // For now, we'll just use a placeholder
        }

        if ($request->filled('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        if ($request->filled('on_sale')) {
            if (\Schema::hasColumn('products', 'discount_percentage')) {
                $query->where('discount_percentage', '>', 0);
            }
        }

        // Apply sorting
        switch ($request->get('sort', 'latest')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->inRandomOrder(); // Replace with actual popularity logic
                break;
            case 'rating':
                $query->inRandomOrder(); // Replace with actual rating logic
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Paginate results
        $products = $query->paginate(12)->withQueryString();

        return view('frontend.products.index', compact('products', 'categories', 'brands'));
    }

    public function show(Product $product)
    {
        // Load relationships
        $product->load(['category', 'images']);

        // Get related products from same category
        $relatedProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }

    public function category(Category $category)
    {
        $products = Product::with(['category', 'images'])
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('frontend.products.category', compact('category', 'products'));
    }

    public function quickView(Product $product)
    {
        $product->load(['category', 'images']);
        
        return view('frontend.products.quick-view', compact('product'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('products.index');
        }

        $products = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where(function($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', '%' . $query . '%')
                           ->orWhere('description', 'like', '%' . $query . '%')
                           ->orWhere('short_description', 'like', '%' . $query . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('frontend.products.search', compact('products', 'query'));
    }
}