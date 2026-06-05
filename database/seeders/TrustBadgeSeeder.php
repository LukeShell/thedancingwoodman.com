<?php

namespace Database\Seeders;

use App\Models\TrustBadge;
use Illuminate\Database\Seeder;

class TrustBadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'icon' => 'truck',
                'title' => 'Free Mainland UK Delivery',
                'subtitle' => 'Estimated 4-6 weeks',
            ],
            [
                'icon' => 'sparkles',
                'title' => 'Sustainable Timber',
                'subtitle' => 'FSC Certified Reclaimed Wood',
            ],
            [
                'icon' => 'shield-check',
                'title' => 'Lifetime Guarantee',
                'subtitle' => 'On structural craftsmanship',
            ],
            [
                'icon' => 'paint-brush',
                'title' => 'Hand-Finished',
                'subtitle' => 'Osmo Polyx-Oil treatments',
            ],
        ];

        foreach ($badges as $index => $attributes) {
            TrustBadge::updateOrCreate(
                ['title' => $attributes['title']],
                array_merge($attributes, ['sort_order' => $index, 'is_active' => true]),
            );
        }
    }
}
