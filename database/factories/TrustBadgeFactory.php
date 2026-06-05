<?php

namespace Database\Factories;

use App\Models\TrustBadge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrustBadge>
 */
class TrustBadgeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icon' => $this->faker->randomElement(['truck', 'sparkles', 'shield-check', 'paint-brush']),
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->sentence(6),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
