<?php

use App\Models\Basket;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;

it('rejects an inactive discount', function () {
    $discount = Discount::factory()->inactive()->create();

    expect($discount->isCurrentlyValid())->toBeFalse();
});

it('rejects a discount whose end date has passed', function () {
    $discount = Discount::factory()->expired()->create();

    expect($discount->isCurrentlyValid())->toBeFalse();
});

it('rejects a discount whose start date is in the future', function () {
    $discount = Discount::factory()->create([
        'starts_at' => now()->addDay(),
    ]);

    expect($discount->isCurrentlyValid())->toBeFalse();
});

it('rejects a discount that has hit its max uses', function () {
    $discount = Discount::factory()->create([
        'max_uses' => 5,
        'times_used' => 5,
    ]);

    expect($discount->isCurrentlyValid())->toBeFalse();
});

it('accepts a discount within its validity window', function () {
    $discount = Discount::factory()->create([
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDay(),
        'max_uses' => 10,
        'times_used' => 3,
    ]);

    expect($discount->isCurrentlyValid())->toBeTrue();
});

it('rejects an otherwise-valid code when the basket subtotal is below the minimum', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 10.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $discount = Discount::factory()->percentage(10)->create([
        'min_subtotal' => 50.00,
    ]);

    expect($discount->isUsable($basket->fresh()))->toBeFalse();
});

it('accepts a discount when the basket subtotal meets the minimum', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $discount = Discount::factory()->percentage(10)->create([
        'min_subtotal' => 50.00,
    ]);

    expect($discount->isUsable($basket->fresh()))->toBeTrue();
});
