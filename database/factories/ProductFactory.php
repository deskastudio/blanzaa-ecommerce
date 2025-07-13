<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $brands = ['Apple', 'Samsung', 'Xiaomi', 'Huawei', 'Sony', 'LG', 'Asus', 'Acer'];
        $price = fake()->numberBetween(1000000, 25000000);
        
        return [
            'category_id' => Category::inRandomOrder()->first()?->id,
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(),
            'sku' => fake()->unique()->bothify('SKU-###-???'),
            'price' => $price,
            'compare_price' => $price + fake()->numberBetween(500000, 2000000),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'min_stock_level' => fake()->numberBetween(5, 20),
            'weight' => fake()->randomFloat(2, 0.1, 5),
            'dimensions' => fake()->numberBetween(10, 50) . 'x' . fake()->numberBetween(10, 50) . 'x' . fake()->numberBetween(2, 10),
            'brand' => fake()->randomElement($brands),
            'warranty' => fake()->randomElement(['1 Year', '2 Years', '3 Years']) . ' ' . fake()->randomElement(['International', 'Local']) . ' Warranty',
            'is_active' => fake()->boolean(90),
            'is_featured' => fake()->boolean(20),
            'meta_title' => fake()->sentence(),
            'meta_description' => fake()->text(160),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => fake()->numberBetween(1, 5),
        ]);
    }
}