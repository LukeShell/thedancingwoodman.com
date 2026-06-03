<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = $this->faker->numberBetween(500, 10000);
        $quantity = $this->faker->numberBetween(1, 3);

        return [
            'order_id' => Order::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'product_name' => $this->faker->words(3, true),
            'variant_label' => null,
            'sku' => strtoupper($this->faker->bothify('SKU-####')),
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'line_total' => $unitPrice * $quantity,
        ];
    }
}
