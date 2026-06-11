<?php

use App\Livewire\Storefront\BasketPage;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\BasketResolver;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->basket = app(BasketResolver::class)->current();

    $product = Product::factory()->create();
    $this->variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

    $this->basket->addItem($this->variant, [], 2);
});

it('applies a valid discount code and shows the discount in the summary', function () {
    Discount::factory()->percentage(10)->create(['code' => 'WELCOME10']);

    livewire(BasketPage::class)
        ->set('promoCode', 'welcome10')
        ->call('applyPromo')
        ->assertSet('promoError', null)
        ->assertViewHas('discountAmount', 20.0)
        ->assertViewHas('total', 180.0);

    expect($this->basket->fresh()->discount_id)->not->toBeNull();
});

it('rejects a code that does not exist', function () {
    livewire(BasketPage::class)
        ->set('promoCode', 'NOPE')
        ->call('applyPromo')
        ->assertSet('promoError', 'That code is not valid for your basket.');

    expect($this->basket->fresh()->discount_id)->toBeNull();
});

it('rejects an expired code', function () {
    Discount::factory()->percentage(10)->expired()->create(['code' => 'EXPIRED']);

    livewire(BasketPage::class)
        ->set('promoCode', 'EXPIRED')
        ->call('applyPromo')
        ->assertSet('promoError', 'That code is not valid for your basket.');
});

it('rejects a maxed-out code', function () {
    Discount::factory()->percentage(10)->create([
        'code' => 'MAXED',
        'max_uses' => 1,
        'times_used' => 1,
    ]);

    livewire(BasketPage::class)
        ->set('promoCode', 'MAXED')
        ->call('applyPromo')
        ->assertSet('promoError', 'That code is not valid for your basket.');
});

it('removes a discount and restores the original total', function () {
    $discount = Discount::factory()->percentage(10)->create(['code' => 'WELCOME10']);
    $this->basket->forceFill(['discount_id' => $discount->id])->save();

    livewire(BasketPage::class)
        ->call('removePromo')
        ->assertViewHas('total', 200.0);

    expect($this->basket->fresh()->discount_id)->toBeNull();
});
