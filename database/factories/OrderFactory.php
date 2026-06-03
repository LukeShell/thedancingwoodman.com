<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(2000, 50000);

        return [
            'reference' => 'TDW-'.strtoupper(Str::random(8)),
            'status' => OrderStatus::Pending,
            'currency' => 'GBP',
            'subtotal' => $subtotal,
            'shipping_total' => 0,
            'tax_total' => 0,
            'grand_total' => $subtotal,
            'email' => $this->faker->safeEmail(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => null,
            'city' => $this->faker->city(),
            'country' => 'GB',
            'state' => null,
            'postal_code' => $this->faker->postcode(),
            'placed_at' => now(),
        ];
    }

    public function paid(): self
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    public function awaitingPayment(): self
    {
        return $this->state(fn () => [
            'status' => OrderStatus::AwaitingPayment,
        ]);
    }
}
