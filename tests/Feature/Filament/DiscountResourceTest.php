<?php

use App\Enums\DiscountType;
use App\Filament\Resources\Discounts\Pages\CreateDiscount;
use App\Filament\Resources\Discounts\Pages\EditDiscount;
use App\Filament\Resources\Discounts\Pages\ListDiscounts;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists discounts', function () {
    $discounts = Discount::factory()->count(3)->create();

    livewire(ListDiscounts::class)
        ->assertCanSeeTableRecords($discounts);
});

it('creates a discount and uppercases the code', function () {
    livewire(CreateDiscount::class)
        ->fillForm([
            'code' => 'welcome10',
            'description' => 'Welcome new customers',
            'type' => DiscountType::Percentage->value,
            'value' => 10,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Discount::class, [
        'code' => 'WELCOME10',
        'type' => DiscountType::Percentage->value,
        'value' => 10,
    ]);
});

it('edits a discount and attaches excluded products and categories', function () {
    $discount = Discount::factory()->create();
    $product = Product::factory()->create();
    $category = Category::factory()->create();

    livewire(EditDiscount::class, ['record' => $discount->id])
        ->fillForm([
            'excludedProducts' => [$product->id],
            'excludedCategories' => [$category->id],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($discount->fresh()->excludedProducts->pluck('id')->all())->toContain($product->id)
        ->and($discount->fresh()->excludedCategories->pluck('id')->all())->toContain($category->id);
});

it('validates required fields', function () {
    livewire(CreateDiscount::class)
        ->fillForm([
            'code' => null,
            'value' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'code' => 'required',
            'value' => 'required',
        ]);
});

it('rejects a duplicate code', function () {
    Discount::factory()->create(['code' => 'DUPE']);

    livewire(CreateDiscount::class)
        ->fillForm([
            'code' => 'DUPE',
            'type' => DiscountType::Percentage->value,
            'value' => 5,
        ])
        ->call('create')
        ->assertHasFormErrors(['code']);
});
