<?php

use App\Models\Basket;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\DiscountCalculator;

beforeEach(function () {
    $this->calculator = app(DiscountCalculator::class);
});

it('applies a percentage discount to the whole eligible subtotal', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 2);

    $discount = Discount::factory()->percentage(10)->create();

    expect($this->calculator->amountPence($basket, $discount))->toBe(2000);
});

it('applies a fixed discount when the eligible subtotal covers it', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $discount = Discount::factory()->fixed(25)->create();

    expect($this->calculator->amountPence($basket, $discount))->toBe(2500);
});

it('clamps a fixed discount to the eligible subtotal', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 10.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $discount = Discount::factory()->fixed(25)->create();

    expect($this->calculator->amountPence($basket, $discount))->toBe(1000);
});

it('excludes a product from the eligible subtotal', function () {
    $included = Product::factory()->create();
    $includedVariant = ProductVariant::factory()->for($included)->create(['price' => 50.00]);

    $excluded = Product::factory()->create();
    $excludedVariant = ProductVariant::factory()->for($excluded)->create(['price' => 200.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($includedVariant, [], 1);
    $basket->addItem($excludedVariant, [], 1);

    $discount = Discount::factory()->percentage(10)->create();
    $discount->excludedProducts()->attach($excluded->id);

    expect($this->calculator->eligibleSubtotalPence($basket->fresh(), $discount))->toBe(5000)
        ->and($this->calculator->amountPence($basket->fresh(), $discount))->toBe(500);
});

it('excludes an entire category from the eligible subtotal', function () {
    $excludedCategory = Category::factory()->create();
    $excludedProduct = Product::factory()->create();
    $excludedProduct->categories()->attach($excludedCategory->id);
    $excludedVariant = ProductVariant::factory()->for($excludedProduct)->create(['price' => 200.00]);

    $okProduct = Product::factory()->create();
    $okVariant = ProductVariant::factory()->for($okProduct)->create(['price' => 100.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($excludedVariant, [], 1);
    $basket->addItem($okVariant, [], 1);

    $discount = Discount::factory()->percentage(50)->create();
    $discount->excludedCategories()->attach($excludedCategory->id);

    expect($this->calculator->amountPence($basket->fresh(), $discount))->toBe(5000);
});

it('returns zero when all items are excluded', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $discount = Discount::factory()->fixed(50)->create();
    $discount->excludedProducts()->attach($product->id);

    expect($this->calculator->amountPence($basket->fresh(), $discount))->toBe(0);
});
