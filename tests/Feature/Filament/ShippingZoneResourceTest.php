<?php

use App\Enums\ShippingMethodType;
use App\Filament\Resources\ShippingZones\Pages\CreateShippingZone;
use App\Filament\Resources\ShippingZones\Pages\EditShippingZone;
use App\Filament\Resources\ShippingZones\Pages\ListShippingZones;
use App\Models\ShippingZone;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists shipping zones', function () {
    $zones = ShippingZone::factory()->count(3)->create();

    livewire(ListShippingZones::class)
        ->assertCanSeeTableRecords($zones);
});

it('creates a flat-rate zone', function () {
    livewire(CreateShippingZone::class)
        ->fillForm([
            'name' => 'Northern Ireland',
            'country_code' => 'GB',
            'postcode_patterns' => ['BT*'],
            'priority' => 10,
            'is_active' => true,
            'method_type' => ShippingMethodType::Flat->value,
            'flat_rate' => 75.00,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(ShippingZone::query()->where('name', 'Northern Ireland')->first())
        ->not->toBeNull()
        ->method_type->toBe(ShippingMethodType::Flat)
        ->flat_rate->toBe(7500)
        ->postcode_patterns->toBe(['BT*']);
});

it('edits an existing zone', function () {
    $zone = ShippingZone::factory()->create([
        'name' => 'Old',
        'flat_rate' => 1000,
    ]);

    livewire(EditShippingZone::class, ['record' => $zone->getRouteKey()])
        ->fillForm(['name' => 'New name'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($zone->fresh()->name)->toBe('New name');
});
