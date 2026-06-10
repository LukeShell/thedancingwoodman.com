<?php

use App\Livewire\Storefront\BasketPage;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductVariant;
use App\Support\BasketResolver;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->basket = app(BasketResolver::class)->current();

    $product = Product::factory()->create();
    $this->variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);
    $this->addon = ProductAddon::factory()->for($product)->create(['price' => 25.00]);
});

it('lists items and computes the subtotal', function () {
    $this->basket->addItem($this->variant, [$this->addon->id], 2);

    livewire(BasketPage::class)
        ->assertSee($this->variant->product->name)
        ->assertViewHas('subtotal', 250.00);
});

it('updates the quantity of a line', function () {
    $item = $this->basket->addItem($this->variant, [], 1);

    livewire(BasketPage::class)
        ->call('updateQuantity', $item->id, 5)
        ->assertDispatched('basket-updated');

    expect($item->fresh()->quantity)->toBe(5);
});

it('removes a line when quantity drops below 1', function () {
    $item = $this->basket->addItem($this->variant, [], 1);

    livewire(BasketPage::class)
        ->call('updateQuantity', $item->id, 0);

    expect($item->fresh())->toBeNull();
});

it('removes a line via removeItem', function () {
    $item = $this->basket->addItem($this->variant, [], 1);

    livewire(BasketPage::class)
        ->call('removeItem', $item->id)
        ->assertDispatched('basket-updated');

    expect($item->fresh())->toBeNull();
});

it('shows the empty state when no items exist', function () {
    livewire(BasketPage::class)
        ->assertSee('Your basket is empty.');
});

it('increments a line quantity', function () {
    $item = $this->basket->addItem($this->variant, [], 1);

    livewire(BasketPage::class)
        ->call('incrementItem', $item->id)
        ->assertDispatched('basket-updated');

    expect($item->fresh()->quantity)->toBe(2);
});

it('decrements a line quantity', function () {
    $item = $this->basket->addItem($this->variant, [], 2);

    livewire(BasketPage::class)
        ->call('decrementItem', $item->id)
        ->assertDispatched('basket-updated');

    expect($item->fresh()->quantity)->toBe(1);
});

it('removes a line when decremented below 1', function () {
    $item = $this->basket->addItem($this->variant, [], 1);

    livewire(BasketPage::class)
        ->call('decrementItem', $item->id);

    expect($item->fresh())->toBeNull();
});

it('renders the order summary with shipping copy', function () {
    $this->basket->addItem($this->variant, [], 1);

    livewire(BasketPage::class)
        ->assertSee('Order Summary')
        ->assertSee('Calculated at checkout')
        ->assertSee('Proceed to Checkout');
});

it('accepts applyPromo without erroring', function () {
    $this->basket->addItem($this->variant, [], 1);

    livewire(BasketPage::class)
        ->set('promoCode', 'WELCOME10')
        ->call('applyPromo')
        ->assertHasNoErrors();
});
