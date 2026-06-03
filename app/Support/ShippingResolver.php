<?php

namespace App\Support;

use App\Models\ShippingZone;

class ShippingResolver
{
    public function resolve(?string $countryCode, ?string $postcode, int $subtotal): ?ShippingQuote
    {
        $country = strtoupper(trim((string) $countryCode));

        if ($country === '') {
            return null;
        }

        $zones = ShippingZone::query()
            ->where('is_active', true)
            ->whereIn('country_code', [$country, ShippingZone::ANY_COUNTRY])
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        foreach ($zones as $zone) {
            if (! $zone->matchesPostcode($postcode)) {
                continue;
            }

            $cost = $zone->costFor($subtotal);

            if ($cost === null) {
                continue;
            }

            return new ShippingQuote(
                zoneId: $zone->id,
                zoneName: $zone->name,
                methodType: $zone->method_type,
                costPence: $cost,
            );
        }

        return null;
    }
}
