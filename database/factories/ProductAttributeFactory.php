<?php

namespace Database\Factories;

use App\Enums\AttributeDisplayType;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductAttribute>
 */
class ProductAttributeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => fake()->randomElement(['Diameter', 'Length', 'Depth', 'Finish']),
            'display_type' => AttributeDisplayType::Dropdown,
            'sort_order' => 0,
        ];
    }
}
