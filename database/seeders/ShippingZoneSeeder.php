<?php

namespace Database\Seeders;

use App\Enums\ShippingMethodType;
use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Northern Ireland',
                'country_code' => 'GB',
                'postcode_patterns' => ['BT*'],
                'priority' => 10,
                'method_type' => ShippingMethodType::Flat,
                'flat_rate' => 7500,
            ],
            [
                'name' => 'Scottish Highlands',
                'country_code' => 'GB',
                'postcode_patterns' => ['IV*', 'KW*', 'HS*', 'ZE*', 'PA*', 'PH*'],
                'priority' => 20,
                'method_type' => ShippingMethodType::Flat,
                'flat_rate' => 6500,
            ],
            [
                'name' => 'Isle of Wight',
                'country_code' => 'GB',
                'postcode_patterns' => ['PO30*', 'PO31*', 'PO32*', 'PO33*', 'PO34*', 'PO35*', 'PO36*', 'PO37*', 'PO38*', 'PO39*', 'PO40*', 'PO41*'],
                'priority' => 20,
                'method_type' => ShippingMethodType::Flat,
                'flat_rate' => 5000,
            ],
            [
                'name' => 'Isle of Man',
                'country_code' => 'GB',
                'postcode_patterns' => ['IM*'],
                'priority' => 20,
                'method_type' => ShippingMethodType::Flat,
                'flat_rate' => 7500,
            ],
            [
                'name' => 'United Kingdom',
                'country_code' => 'GB',
                'postcode_patterns' => null,
                'priority' => 100,
                'method_type' => ShippingMethodType::Free,
                'flat_rate' => null,
                'free_min_subtotal' => null,
            ],
            [
                'name' => 'Rest of the world',
                'country_code' => ShippingZone::ANY_COUNTRY,
                'postcode_patterns' => null,
                'priority' => 1000,
                'method_type' => ShippingMethodType::Flat,
                'flat_rate' => 25000,
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::updateOrCreate(
                ['name' => $zone['name']],
                array_merge(['is_active' => true, 'free_min_subtotal' => null], $zone),
            );
        }
    }
}
