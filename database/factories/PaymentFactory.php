<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'gateway' => 'stripe',
            'gateway_payment_id' => 'pi_'.$this->faker->bothify('??????????????????????????'),
            'gateway_customer_id' => null,
            'status' => PaymentStatus::Processing,
            'amount' => $this->faker->numberBetween(1000, 50000),
            'currency' => 'GBP',
            'payment_method_type' => null,
            'client_secret' => 'pi_'.$this->faker->bothify('????????').'_secret_'.$this->faker->bothify('????????'),
            'metadata' => [],
        ];
    }

    public function succeeded(): self
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::Succeeded,
            'processed_at' => now(),
        ]);
    }
}
