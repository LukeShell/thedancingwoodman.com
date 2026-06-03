<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => Str::upper(Str::random(10)),
            'price' => fake()->randomFloat(2, 50, 2500),
            'stock_quantity' => fake()->numberBetween(0, 12),
            'is_active' => true,
        ];
    }
}
