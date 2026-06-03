<?php

namespace App\Models;

use App\Enums\ShippingMethodType;
use Database\Factories\ShippingZoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    /** @use HasFactory<ShippingZoneFactory> */
    use HasFactory;

    public const ANY_COUNTRY = '*';

    protected $fillable = [
        'name',
        'country_code',
        'postcode_patterns',
        'priority',
        'is_active',
        'method_type',
        'flat_rate',
        'free_min_subtotal',
    ];

    protected $casts = [
        'postcode_patterns' => 'array',
        'priority' => 'int',
        'is_active' => 'bool',
        'method_type' => ShippingMethodType::class,
        'flat_rate' => 'int',
        'free_min_subtotal' => 'int',
    ];

    public function matchesPostcode(?string $postcode): bool
    {
        $patterns = $this->postcode_patterns ?? [];

        if ($patterns === []) {
            return true;
        }

        $normalized = strtoupper(preg_replace('/\s+/', '', (string) $postcode));

        if ($normalized === '') {
            return false;
        }

        foreach ($patterns as $pattern) {
            $regex = '/^'.str_replace('\*', '.*', preg_quote(strtoupper((string) $pattern), '/')).'$/';

            if (preg_match($regex, $normalized) === 1) {
                return true;
            }
        }

        return false;
    }

    public function costFor(int $subtotal): ?int
    {
        return match ($this->method_type) {
            ShippingMethodType::Free => $this->isThresholdMet($subtotal) ? 0 : null,
            ShippingMethodType::Flat => (int) ($this->flat_rate ?? 0),
        };
    }

    private function isThresholdMet(int $subtotal): bool
    {
        if ($this->free_min_subtotal === null) {
            return true;
        }

        return $subtotal >= (int) $this->free_min_subtotal;
    }
}
