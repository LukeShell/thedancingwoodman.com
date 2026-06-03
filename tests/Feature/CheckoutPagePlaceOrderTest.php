<?php

use App\Livewire\Storefront\CheckoutPage;
use App\Models\Basket;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Payments\PaymentManager;
use App\Support\BasketResolver;
use Livewire\Livewire;
use Tests\Support\FakePaymentGateway;

beforeEach(function () {
    $manager = new PaymentManager('stripe');
    $manager->register(new FakePaymentGateway);
    $this->app->instance(PaymentManager::class, $manager);

    seedUkFreeShipping();
});

function seedBasket(): Basket
{
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 75.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [], 1);

    $resolver = new class($basket) extends BasketResolver
    {
        public function __construct(private readonly Basket $stub) {}

        public function current(): Basket
        {
            return $this->stub;
        }
    };

    app()->instance(BasketResolver::class, $resolver);

    return $basket;
}

it('creates an order and dispatches payment-intent-created on placeOrder', function () {
    seedBasket();

    Livewire::test(CheckoutPage::class)
        ->set('email', 'buyer@example.com')
        ->set('firstName', 'Pat')
        ->set('lastName', 'Buyer')
        ->set('addressLine1', '12 Lane')
        ->set('city', 'Bristol')
        ->set('country', 'GB')
        ->set('postalCode', 'BS1 1AA')
        ->call('placeOrder')
        ->assertHasNoErrors()
        ->assertDispatched('payment-intent-created', function (string $name, array $params) {
            return ($params['gateway'] ?? null) === 'stripe'
                && is_string($params['clientSecret'] ?? null)
                && str_starts_with($params['clientSecret'], 'pi_fake_')
                && ($params['publishableKey'] ?? null) === 'pk_test_fake'
                && str_contains((string) ($params['returnUrl'] ?? ''), '/confirm');
        });

    expect(Order::count())->toBe(1);
});

it('validates required fields before placing the order', function () {
    seedBasket();

    Livewire::test(CheckoutPage::class)
        ->set('email', '')
        ->set('firstName', '')
        ->call('placeOrder')
        ->assertHasErrors(['email', 'firstName']);

    expect(Order::count())->toBe(0);
});
