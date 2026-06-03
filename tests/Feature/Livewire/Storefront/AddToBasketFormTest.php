<?php

use App\Livewire\Storefront\AddToBasketForm;
use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductVariant;

use function Pest\Livewire\livewire;

it('adds a variant with addons to the basket', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 200.00]);
    $addon = ProductAddon::factory()->for($product)->create(['price' => 50.00]);

    livewire(AddToBasketForm::class, ['product' => $product])
        ->set('selectedVariantId', $variant->id)
        ->set('selectedAddonIds', [$addon->id])
        ->set('quantity', 2)
        ->call('addToBasket')
        ->assertDispatched('basket-updated')
        ->assertHasNoErrors();

    expect(Basket::count())->toBe(1)
        ->and(BasketItem::count())->toBe(1);

    $item = BasketItem::first();
    expect($item->quantity)->toBe(2)
        ->and($item->product_variant_id)->toBe($variant->id)
        ->and($item->addons->pluck('id')->all())->toBe([$addon->id]);
});

it('rejects an addon belonging to a different product', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create();
    $foreignAddon = ProductAddon::factory()->for(Product::factory())->create();

    livewire(AddToBasketForm::class, ['product' => $product])
        ->set('selectedVariantId', $variant->id)
        ->set('selectedAddonIds', [$foreignAddon->id])
        ->call('addToBasket')
        ->assertHasNoErrors();

    $item = BasketItem::first();
    expect($item)->not->toBeNull()
        ->and($item->addons)->toHaveCount(0);
});

it('validates that a variant is selected', function () {
    $product = Product::factory()->create();

    livewire(AddToBasketForm::class, ['product' => $product])
        ->set('selectedVariantId', null)
        ->call('addToBasket')
        ->assertHasErrors(['selectedVariantId']);
});
