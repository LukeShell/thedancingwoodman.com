<?php

use App\Actions\Orders\PlaceOrderFromBasket;
use App\Enums\ShippingMethodType;
use App\Models\Basket;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Payments\Exceptions\PaymentException;
use App\Payments\PaymentManager;
use Tests\Support\FakePaymentGateway;

beforeEach(function () {
    $manager = new PaymentManager('stripe');
    $manager->register(new FakePaymentGateway);
    $this->app->instance(PaymentManager::class, $manager);
});

it('adds the resolved shipping cost to the order grand total', function () {
    $zone = ShippingZone::factory()->create([
        'name' => 'Northern Ireland',
        'country_code' => 'GB',
        'postcode_patterns' => ['BT*'],
        'priority' => 10,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 7500,
    ]);

    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $result = app(PlaceOrderFromBasket::class)($basket, [
        'email' => 'buyer@example.com',
        'first_name' => 'Pat',
        'last_name' => 'Buyer',
        'address_line_1' => '12 Lane',
        'city' => 'Belfast',
        'country' => 'GB',
        'postal_code' => 'BT15 1AA',
    ]);

    $order = $result['order'];

    expect($order->subtotal)->toBe(10000)
        ->and($order->shipping_total)->toBe(7500)
        ->and($order->grand_total)->toBe(17500)
        ->and($order->shipping_zone_id)->toBe($zone->id)
        ->and($order->shipping_method_name)->toBe('Northern Ireland');
});

it('snapshots the zone name so renaming the zone later does not change the order', function () {
    $zone = ShippingZone::factory()->create([
        'name' => 'Original name',
        'country_code' => 'GB',
        'priority' => 100,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 500,
    ]);

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
        'postal_code' => 'BS1 1AA',
    ]);

    $zone->update(['name' => 'Renamed']);

    expect($result['order']->shipping_method_name)->toBe('Original name');
});

it('refuses to place an order when no shipping zone matches', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 10.00]);
    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    app(PlaceOrderFromBasket::class)($basket, [
        'email' => 'a@b.com',
        'first_name' => 'A',
        'last_name' => 'B',
        'address_line_1' => '1',
        'city' => 'X',
        'country' => 'FR',
        'postal_code' => '75001',
    ]);
})->throws(PaymentException::class, 'We are unable to ship');
