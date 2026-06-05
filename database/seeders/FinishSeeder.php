<?php

namespace Database\Seeders;

use App\Models\Finish;
use Illuminate\Database\Seeder;

class FinishSeeder extends Seeder
{
    public function run(): void
    {
        $finishes = [
            ['name' => 'Natural', 'slug' => 'natural', 'hex_color' => '#E8DCC4'],
            ['name' => 'Honey', 'slug' => 'honey', 'hex_color' => '#D4A76A'],
            ['name' => 'Clear', 'slug' => 'clear', 'hex_color' => '#F5F5F5'],
            ['name' => 'Antique Oak', 'slug' => 'antique-oak', 'hex_color' => '#5D4037'],
        ];

        foreach ($finishes as $index => $attributes) {
            Finish::updateOrCreate(
                ['slug' => $attributes['slug']],
                array_merge($attributes, ['sort_order' => $index, 'is_active' => true]),
            );
        }
    }
}
