<?php

use App\Models\Basket;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductVariant;
use App\Support\BasketResolver;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Crypt;

it('merges lines with the same variant and addon set', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);
    $addon = ProductAddon::factory()->for($product)->create(['price' => 25.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [$addon->id], 1);
    $basket->addItem($variant, [$addon->id], 2);

    $basket->refresh();

    expect($basket->items)->toHaveCount(1)
        ->and($basket->items->first()->quantity)->toBe(3);
});

it('creates separate lines when addon sets differ', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);
    $addonA = ProductAddon::factory()->for($product)->create();
    $addonB = ProductAddon::factory()->for($product)->create();

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [$addonA->id], 1);
    $basket->addItem($variant, [$addonA->id, $addonB->id], 1);

    expect($basket->fresh()->items)->toHaveCount(2);
});

it('treats addon order as irrelevant when matching lines', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create();
    $addonA = ProductAddon::factory()->for($product)->create();
    $addonB = ProductAddon::factory()->for($product)->create();

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [$addonA->id, $addonB->id], 1);
    $basket->addItem($variant, [$addonB->id, $addonA->id], 2);

    $basket->refresh();

    expect($basket->items)->toHaveCount(1)
        ->and($basket->items->first()->quantity)->toBe(3);
});

it('computes subtotal from current variant and addon prices', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);
    $addon = ProductAddon::factory()->for($product)->create(['price' => 25.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [$addon->id], 2);

    expect($basket->subtotal())->toBe('250.00')
        ->and($basket->itemCount())->toBe(2);
});

it('cascades item deletion when basket is deleted', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create();
    $addon = ProductAddon::factory()->for($product)->create();

    $basket = Basket::factory()->create();
    $item = $basket->addItem($variant, [$addon->id], 1);

    $basket->delete();

    expect(DB::table('basket_items')->where('id', $item->id)->count())->toBe(0)
        ->and(DB::table('basket_item_product_addon')->where('basket_item_id', $item->id)->count())->toBe(0);
});

it('returns the same basket on repeat resolver calls within a request', function () {
    $resolver = app(BasketResolver::class);

    $first = $resolver->current();
    $second = $resolver->current();

    expect($first->id)->toBe($second->id);
});

it('reuses an existing basket when the cookie token matches', function () {
    $existing = Basket::factory()->create();

    $this->withCookie('basket_token', $existing->token);

    $this->get('/')->assertOk();

    expect(Basket::count())->toBe(1);
});

it('creates a new basket and queues a cookie when none exists', function () {
    expect(Basket::count())->toBe(0);

    $response = $this->get('/');

    $response->assertOk();
    expect(Basket::count())->toBe(1);

    $cookie = collect($response->headers->getCookies())
        ->firstWhere(fn ($c) => $c->getName() === 'basket_token');

    expect($cookie)->not->toBeNull();

    $decrypted = CookieValuePrefix::remove(Crypt::decrypt($cookie->getValue(), false));

    expect($decrypted)->toBe(Basket::first()->token);
});
