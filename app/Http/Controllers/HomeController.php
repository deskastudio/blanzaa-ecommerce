<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Get top categories with product count
            $topCategories = Category::withCount('products')
                ->where('is_active', true)
                ->orderBy('products_count', 'desc')
                ->take(10)
                ->get();

            // Get featured products - hanya gunakan kolom yang pasti ada
            $featuredProducts = Product::with(['category', 'images'])
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->where(function($query) {
                    // Cek kolom discount_percentage dan is_featured jika ada
                    if (\Schema::hasColumn('products', 'discount_percentage')) {
                        $query->where('discount_percentage', '>', 0);
                    }
                    if (\Schema::hasColumn('products', 'is_featured')) {
                        $query->orWhere('is_featured', true);
                    }
                })
                ->take(6)
                ->get();

            // Get flash sale products - hanya jika kolom discount_percentage ada
            $flashSaleProducts = collect();
            if (\Schema::hasColumn('products', 'discount_percentage')) {
                $flashSaleProducts = Product::with(['category', 'images'])
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->where('discount_percentage', '>', 20)
                    ->orderBy('discount_percentage', 'desc')
                    ->take(8)
                    ->get();
            }

            // Get best selling products - gunakan random order dulu
            $bestSellingProducts = Product::with(['category', 'images'])
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->inRandomOrder()
                ->take(8)
                ->get();

            // Get latest products
            $latestProducts = Product::with(['category', 'images'])
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            return view('frontend.home', compact(
                'topCategories',
                'featuredProducts', 
                'flashSaleProducts',
                'bestSellingProducts',
                'latestProducts'
            ));

        } catch (\Exception $e) {
            // Fallback jika ada error - gunakan data dummy
            \Log::error('HomeController error: ' . $e->getMessage());
            
            return $this->fallbackView();
        }
    }

    private function fallbackView()
    {
        // Data dummy untuk testing
        $topCategories = collect([
            (object)[
                'id' => 1,
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'products_count' => 25
            ],
            (object)[
                'id' => 2,
                'name' => 'Laptops', 
                'slug' => 'laptops',
                'products_count' => 18
            ],
            (object)[
                'id' => 3,
                'name' => 'Tablets',
                'slug' => 'tablets', 
                'products_count' => 12
            ],
        ]);

        $featuredProducts = collect();
        $flashSaleProducts = collect();
        $bestSellingProducts = collect();
        $latestProducts = collect();

        return view('frontend.home', compact(
            'topCategories',
            'featuredProducts', 
            'flashSaleProducts',
            'bestSellingProducts',
            'latestProducts'
        ));
    }
}