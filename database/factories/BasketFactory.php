<?php

namespace Database\Factories;

use App\Models\Basket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Basket>
 */
class BasketFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => Str::random(40),
        ];
    }
}
