<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category, Request $request)
    {
        // Get products in this category
        $query = Product::with(['category', 'images'])
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0);

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

        $products = $query->paginate(12)->withQueryString();

        return view('frontend.products.category', compact('category', 'products'));
    }
}