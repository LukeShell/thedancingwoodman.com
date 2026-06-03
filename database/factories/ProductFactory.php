<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.Str::random(5),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'base_price' => fake()->randomFloat(2, 50, 1500),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
