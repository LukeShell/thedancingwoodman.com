<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductAddon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductAddon>
 */
class ProductAddonFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->randomElement(['Matching bench', 'Cushion set', 'Glass top', 'Delivery upgrade']),
            'description' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 20, 400),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
