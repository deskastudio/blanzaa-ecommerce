<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $smartphones = Category::where('name', 'Smartphone')->first();
        $laptops = Category::where('name', 'Laptop')->first();
        $tablets = Category::where('name', 'Tablet')->first();

        $products = [
            [
                'category_id' => $smartphones->id,
                'name' => 'iPhone 15 Pro',
                'sku' => 'IP15-PRO-128',
                'description' => 'Latest iPhone 15 Pro with A17 Pro chip',
                'short_description' => 'Premium smartphone with advanced camera system',
                'price' => 15999000,
                'compare_price' => 17999000,
                'stock_quantity' => 25,
                'brand' => 'Apple',
                'warranty' => '1 Year International Warranty',
                'is_featured' => true,
            ],
            [
                'category_id' => $smartphones->id,
                'name' => 'Samsung Galaxy S24',
                'sku' => 'SAM-S24-256',
                'description' => 'Galaxy S24 with AI-powered photography',
                'short_description' => 'Flagship Android smartphone',
                'price' => 12999000,
                'compare_price' => 14999000,
                'stock_quantity' => 30,
                'brand' => 'Samsung',
                'warranty' => '1 Year Local Warranty',
                'is_featured' => true,
            ],
            [
                'category_id' => $laptops->id,
                'name' => 'MacBook Air M3',
                'sku' => 'MBA-M3-256',
                'description' => 'MacBook Air with M3 chip for ultimate performance',
                'short_description' => 'Ultra-thin laptop with all-day battery',
                'price' => 18999000,
                'compare_price' => 20999000,
                'stock_quantity' => 15,
                'brand' => 'Apple',
                'warranty' => '1 Year International Warranty',
                'is_featured' => true,
            ],
            [
                'category_id' => $tablets->id,
                'name' => 'iPad Pro 12.9"',
                'sku' => 'IPAD-PRO-129',
                'description' => 'iPad Pro with M2 chip and Liquid Retina XDR display',
                'short_description' => 'Professional tablet for creative work',
                'price' => 16999000,
                'stock_quantity' => 20,
                'brand' => 'Apple',
                'warranty' => '1 Year International Warranty',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}