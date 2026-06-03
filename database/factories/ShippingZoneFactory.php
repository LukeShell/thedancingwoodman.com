<?php

namespace Database\Factories;

use App\Enums\ShippingMethodType;
use App\Models\ShippingZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingZone>
 */
class ShippingZoneFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'country_code' => 'GB',
            'postcode_patterns' => null,
            'priority' => 100,
            'is_active' => true,
            'method_type' => ShippingMethodType::Flat,
            'flat_rate' => 2500,
            'free_min_subtotal' => null,
        ];
    }

    public function free(): self
    {
        return $this->state(fn () => [
            'method_type' => ShippingMethodType::Free,
            'flat_rate' => null,
        ]);
    }

    /**
     * @param  array<int, string>  $patterns
     */
    public function patterns(array $patterns): self
    {
        return $this->state(fn () => ['postcode_patterns' => $patterns]);
    }
}
