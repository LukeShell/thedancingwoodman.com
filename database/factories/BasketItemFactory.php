<?php

namespace Database\Factories;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BasketItem>
 */
class BasketItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'basket_id' => Basket::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'quantity' => 1,
        ];
    }
}
