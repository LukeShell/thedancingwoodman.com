<?php

use App\Enums\ShippingMethodType;
use App\Models\ShippingZone;
use App\Support\ShippingResolver;

function seedScreenshotZones(): void
{
    ShippingZone::factory()->create([
        'name' => 'Northern Ireland',
        'country_code' => 'GB',
        'postcode_patterns' => ['BT*'],
        'priority' => 10,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 7500,
    ]);

    ShippingZone::factory()->create([
        'name' => 'Scottish Highlands',
        'country_code' => 'GB',
        'postcode_patterns' => ['IV*', 'KW*'],
        'priority' => 20,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 6500,
    ]);

    ShippingZone::factory()->free()->create([
        'name' => 'United Kingdom',
        'country_code' => 'GB',
        'postcode_patterns' => null,
        'priority' => 100,
    ]);

    ShippingZone::factory()->create([
        'name' => 'Rest of the world',
        'country_code' => ShippingZone::ANY_COUNTRY,
        'postcode_patterns' => null,
        'priority' => 1000,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 25000,
    ]);
}

it('matches the UK free zone for a mainland postcode', function () {
    seedScreenshotZones();

    $quote = app(ShippingResolver::class)->resolve('GB', 'BS1 1AA', 10000);

    expect($quote)->not->toBeNull()
        ->and($quote->zoneName)->toBe('United Kingdom')
        ->and($quote->isFree())->toBeTrue()
        ->and($quote->costPence)->toBe(0);
});

it('matches Northern Ireland by postcode wildcard ahead of the UK zone', function () {
    seedScreenshotZones();

    $quote = app(ShippingResolver::class)->resolve('GB', 'BT15 1AA', 10000);

    expect($quote->zoneName)->toBe('Northern Ireland')
        ->and($quote->costPence)->toBe(7500);
});

it('matches Scottish Highlands by IV postcode', function () {
    seedScreenshotZones();

    $quote = app(ShippingResolver::class)->resolve('GB', 'IV3 5XX', 10000);

    expect($quote->zoneName)->toBe('Scottish Highlands')
        ->and($quote->costPence)->toBe(6500);
});

it('falls through to Rest of the world for foreign countries', function () {
    seedScreenshotZones();

    $quote = app(ShippingResolver::class)->resolve('FR', '75001', 10000);

    expect($quote->zoneName)->toBe('Rest of the world')
        ->and($quote->costPence)->toBe(25000);
});

it('respects priority ordering — lower priority wins', function () {
    ShippingZone::factory()->create([
        'name' => 'Generic',
        'country_code' => 'GB',
        'postcode_patterns' => null,
        'priority' => 100,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 1000,
    ]);

    ShippingZone::factory()->create([
        'name' => 'Specific',
        'country_code' => 'GB',
        'postcode_patterns' => ['BS*'],
        'priority' => 10,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 500,
    ]);

    $quote = app(ShippingResolver::class)->resolve('GB', 'BS1 1AA', 10000);

    expect($quote->zoneName)->toBe('Specific');
});

it('skips inactive zones', function () {
    ShippingZone::factory()->free()->create([
        'name' => 'Inactive UK',
        'country_code' => 'GB',
        'priority' => 50,
        'is_active' => false,
    ]);

    ShippingZone::factory()->create([
        'name' => 'Active UK',
        'country_code' => 'GB',
        'priority' => 100,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 1000,
    ]);

    $quote = app(ShippingResolver::class)->resolve('GB', 'BS1 1AA', 10000);

    expect($quote->zoneName)->toBe('Active UK');
});

it('skips free zones when the basket does not meet the threshold', function () {
    ShippingZone::factory()->free()->create([
        'name' => 'UK over £500',
        'country_code' => 'GB',
        'priority' => 50,
        'free_min_subtotal' => 50000,
    ]);

    ShippingZone::factory()->create([
        'name' => 'UK flat',
        'country_code' => 'GB',
        'priority' => 100,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 1500,
    ]);

    $quote = app(ShippingResolver::class)->resolve('GB', 'BS1 1AA', 10000);

    expect($quote->zoneName)->toBe('UK flat')
        ->and($quote->costPence)->toBe(1500);
});

it('uses the free zone once the threshold is met', function () {
    ShippingZone::factory()->free()->create([
        'name' => 'UK over £500',
        'country_code' => 'GB',
        'priority' => 50,
        'free_min_subtotal' => 50000,
    ]);

    ShippingZone::factory()->create([
        'name' => 'UK flat',
        'country_code' => 'GB',
        'priority' => 100,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 1500,
    ]);

    $quote = app(ShippingResolver::class)->resolve('GB', 'BS1 1AA', 60000);

    expect($quote->zoneName)->toBe('UK over £500')
        ->and($quote->isFree())->toBeTrue();
});

it('returns null when no zone matches', function () {
    ShippingZone::factory()->create([
        'name' => 'UK only',
        'country_code' => 'GB',
        'priority' => 100,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 1000,
    ]);

    $quote = app(ShippingResolver::class)->resolve('FR', '75001', 10000);

    expect($quote)->toBeNull();
});

it('matches postcode patterns case-insensitively and ignoring whitespace', function () {
    ShippingZone::factory()->create([
        'name' => 'NI',
        'country_code' => 'GB',
        'postcode_patterns' => ['BT*'],
        'priority' => 10,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 100,
    ]);

    $quote = app(ShippingResolver::class)->resolve('GB', '  bt15 1aa ', 10000);

    expect($quote->zoneName)->toBe('NI');
});
