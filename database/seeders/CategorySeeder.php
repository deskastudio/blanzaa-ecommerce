<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'description' => 'Latest smartphones and mobile devices',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Laptops and notebooks for work and gaming',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Tablet',
                'slug' => 'tablet',
                'description' => 'Tablets and iPad devices',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Audio',
                'slug' => 'audio',
                'description' => 'Headphones, speakers, and audio equipment',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Gaming',
                'slug' => 'gaming',
                'description' => 'Gaming consoles and accessories',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Phone cases, chargers, and other accessories',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}