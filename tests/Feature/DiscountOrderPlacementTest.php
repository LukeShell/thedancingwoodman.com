<?php

use App\Actions\Orders\PlaceOrderFromBasket;
use App\Models\Basket;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Payments\PaymentManager;
use Tests\Support\FakePaymentGateway;

beforeEach(function () {
    $gateway = new FakePaymentGateway;
    $manager = new PaymentManager('stripe');
    $manager->register($gateway);
    $this->app->instance(PaymentManager::class, $manager);

    seedUkFreeShipping();
});

function placeOrderWithDiscount(Basket $basket): array
{
    return app(PlaceOrderFromBasket::class)($basket, [
        'email' => 'buyer@example.com',
        'first_name' => 'Pat',
        'last_name' => 'Buyer',
        'address_line_1' => '12 Lane',
        'city' => 'Bristol',
        'country' => 'GB',
        'postal_code' => 'BS1 1AA',
    ]);
}

it('snapshots the discount onto the order and increments times_used', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

    $discount = Discount::factory()->percentage(10)->create(['code' => 'WELCOME10']);

    $basket = Basket::factory()->create(['discount_id' => $discount->id]);
    $basket->addItem($variant, [], 2);

    $result = placeOrderWithDiscount($basket);
    $order = $result['order'];

    expect($order->discount_id)->toBe($discount->id)
        ->and($order->discount_code)->toBe('WELCOME10')
        ->and($order->discount_total)->toBe(2000)
        ->and($order->subtotal)->toBe(20000)
        ->and($order->grand_total)->toBe(18000)
        ->and($discount->fresh()->times_used)->toBe(1);
});

it('drops a discount that became invalid before placement', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

    $discount = Discount::factory()->percentage(10)->create([
        'code' => 'EXPIRED',
        'ends_at' => now()->subDay(),
    ]);

    $basket = Basket::factory()->create(['discount_id' => $discount->id]);
    $basket->addItem($variant, [], 1);

    $result = placeOrderWithDiscount($basket);
    $order = $result['order'];

    expect($order->discount_id)->toBeNull()
        ->and($order->discount_code)->toBeNull()
        ->and($order->discount_total)->toBe(0)
        ->and($order->grand_total)->toBe(10000)
        ->and($discount->fresh()->times_used)->toBe(0);
});
