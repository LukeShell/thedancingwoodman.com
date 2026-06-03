<?php

use App\Actions\Orders\PlaceOrderFromBasket;
use App\Enums\OrderStatus;
use App\Models\Basket;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductVariant;
use App\Payments\Exceptions\PaymentException;
use App\Payments\PaymentManager;
use Tests\Support\FakePaymentGateway;

beforeEach(function () {
    $gateway = new FakePaymentGateway;
    $manager = new PaymentManager('stripe');
    $manager->register($gateway);
    $this->app->instance(PaymentManager::class, $manager);
    $this->gateway = $gateway;

    seedUkFreeShipping();
});

it('snapshots variant and addon prices into order_items', function () {
    $product = Product::factory()->create(['name' => 'Walnut Coffee Table']);
    $variant = ProductVariant::factory()->for($product)->create([
        'price' => 199.95,
        'sku' => 'WCT-001',
    ]);
    $addon = ProductAddon::factory()->for($product)->create([
        'name' => 'Wax finish',
        'price' => 19.50,
    ]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [$addon->id], 2);

    $action = app(PlaceOrderFromBasket::class);

    $result = $action($basket, [
        'email' => 'buyer@example.com',
        'first_name' => 'Pat',
        'last_name' => 'Buyer',
        'address_line_1' => '12 Lane',
        'city' => 'Bristol',
        'country' => 'GB',
        'postal_code' => 'BS1 1AA',
    ]);

    $order = $result['order'];

    expect($order->items)->toHaveCount(1)
        ->and($order->items->first()->product_name)->toBe('Walnut Coffee Table')
        ->and($order->items->first()->sku)->toBe('WCT-001')
        ->and($order->items->first()->unit_price)->toBe(21945)
        ->and($order->items->first()->quantity)->toBe(2)
        ->and($order->items->first()->line_total)->toBe(43890)
        ->and($order->items->first()->addons->first()->name)->toBe('Wax finish')
        ->and($order->items->first()->addons->first()->price)->toBe(1950)
        ->and($order->subtotal)->toBe(43890)
        ->and($order->grand_total)->toBe(43890)
        ->and($order->currency)->toBe('GBP');
});

it('marks the basket as converted and prevents reuse', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 50.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $action = app(PlaceOrderFromBasket::class);
    $action($basket, [
        'email' => 'a@b.com',
        'first_name' => 'A',
        'last_name' => 'B',
        'address_line_1' => '1',
        'city' => 'X',
        'country' => 'GB',
        'postal_code' => 'X1 1XX',
    ]);

    expect($basket->fresh()->isConverted())->toBeTrue();

    $action($basket->fresh(), [
        'email' => 'a@b.com',
        'first_name' => 'A',
        'last_name' => 'B',
        'address_line_1' => '1',
        'city' => 'X',
        'country' => 'GB',
        'postal_code' => 'X1 1XX',
    ]);
})->throws(PaymentException::class);

it('does not retroactively change order totals when variant price changes later', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $result = app(PlaceOrderFromBasket::class)($basket, [
        'email' => 'a@b.com',
        'first_name' => 'A',
        'last_name' => 'B',
        'address_line_1' => '1',
        'city' => 'X',
        'country' => 'GB',
        'postal_code' => 'X1 1XX',
    ]);

    $variant->update(['price' => 250.00]);

    $reloaded = $result['order']->fresh('items');

    expect($reloaded->items->first()->unit_price)->toBe(10000)
        ->and($reloaded->grand_total)->toBe(10000);
});

it('transitions the order to AwaitingPayment after creating an intent', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 10.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $result = app(PlaceOrderFromBasket::class)($basket, [
        'email' => 'a@b.com',
        'first_name' => 'A',
        'last_name' => 'B',
        'address_line_1' => '1',
        'city' => 'X',
        'country' => 'GB',
        'postal_code' => 'X1 1XX',
    ]);

    expect($result['order']->fresh()->status)->toBe(OrderStatus::AwaitingPayment)
        ->and($this->gateway->createIntentCalls)->toBe(1);
});
