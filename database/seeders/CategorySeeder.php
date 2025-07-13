<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Smartphone',
                'description' => 'Latest smartphones and mobile devices',
                'sort_order' => 1,
            ],
            [
                'name' => 'Laptop',
                'description' => 'Laptops and notebooks for work and gaming',
                'sort_order' => 2,
            ],
            [
                'name' => 'Tablet',
                'description' => 'Tablets and iPad devices',
                'sort_order' => 3,
            ],
            [
                'name' => 'Audio',
                'description' => 'Headphones, speakers, and audio equipment',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gaming',
                'description' => 'Gaming consoles and accessories',
                'sort_order' => 5,
            ],
            [
                'name' => 'Accessories',
                'description' => 'Phone cases, chargers, and other accessories',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}