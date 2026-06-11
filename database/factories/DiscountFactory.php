<?php

namespace Database\Factories;

use App\Enums\DiscountType;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'description' => $this->faker->sentence(4),
            'type' => DiscountType::Percentage,
            'value' => 10,
            'is_active' => true,
            'stackable' => false,
            'starts_at' => null,
            'ends_at' => null,
            'max_uses' => null,
            'times_used' => 0,
            'min_subtotal' => null,
        ];
    }

    public function percentage(float $percent = 10): self
    {
        return $this->state([
            'type' => DiscountType::Percentage,
            'value' => $percent,
        ]);
    }

    public function fixed(float $amount = 25): self
    {
        return $this->state([
            'type' => DiscountType::Fixed,
            'value' => $amount,
        ]);
    }

    public function expired(): self
    {
        return $this->state([
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDay(),
        ]);
    }

    public function inactive(): self
    {
        return $this->state(['is_active' => false]);
    }
}
