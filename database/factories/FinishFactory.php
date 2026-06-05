<?php

namespace Database\Factories;

use App\Models\Finish;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Finish>
 */
class FinishFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Natural', 'Honey', 'Clear', 'Antique Oak', 'Walnut', 'Whitewashed',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(100, 999),
            'hex_color' => $this->faker->hexColor(),
            'description' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
