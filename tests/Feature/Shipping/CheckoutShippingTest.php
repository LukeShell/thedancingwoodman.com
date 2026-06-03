<?php

use App\Enums\ShippingMethodType;
use App\Livewire\Storefront\CheckoutPage;
use App\Models\Basket;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Payments\PaymentManager;
use App\Support\BasketResolver;
use Tests\Support\FakePaymentGateway;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $manager = new PaymentManager('stripe');
    $manager->register(new FakePaymentGateway);
    $this->app->instance(PaymentManager::class, $manager);
});

function bindShippingTestBasket(): Basket
{
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->for($product)->create(['price' => 100.00]);

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

it('populates a shipping quote when address fields are filled', function () {
    ShippingZone::factory()->free()->create([
        'name' => 'United Kingdom',
        'country_code' => 'GB',
        'priority' => 100,
    ]);

    bindShippingTestBasket();

    livewire(CheckoutPage::class)
        ->set('postalCode', 'BS1 1AA')
        ->assertSet('shippingQuote.zoneName', 'United Kingdom')
        ->assertSet('shippingError', null)
        ->assertSee('Free');
});

it('selects the Northern Ireland flat rate from a BT postcode', function () {
    ShippingZone::factory()->free()->create([
        'name' => 'United Kingdom',
        'country_code' => 'GB',
        'priority' => 100,
    ]);
    ShippingZone::factory()->create([
        'name' => 'Northern Ireland',
        'country_code' => 'GB',
        'postcode_patterns' => ['BT*'],
        'priority' => 10,
        'method_type' => ShippingMethodType::Flat,
        'flat_rate' => 7500,
    ]);

    bindShippingTestBasket();

    livewire(CheckoutPage::class)
        ->set('postalCode', 'BT15 1AA')
        ->assertSet('shippingQuote.zoneName', 'Northern Ireland')
        ->assertSee('£75.00');
});

it('records a shipping error when no zone matches', function () {
    bindShippingTestBasket();

    livewire(CheckoutPage::class)
        ->set('country', 'GB')
        ->set('postalCode', 'BS1 1AA')
        ->assertSet('shippingQuote', null)
        ->assertSet('shippingError', 'We can\'t ship to this address yet. Please check your country and postcode.');
});

it('blocks placing an order when shipping is unavailable', function () {
    bindShippingTestBasket();

    livewire(CheckoutPage::class)
        ->set('email', 'buyer@example.com')
        ->set('firstName', 'Pat')
        ->set('lastName', 'Buyer')
        ->set('addressLine1', '12 Lane')
        ->set('city', 'Bristol')
        ->set('country', 'GB')
        ->set('postalCode', 'BS1 1AA')
        ->call('placeOrder')
        ->assertSet('placementError', 'We can\'t ship to this address yet. Please check your country and postcode.');
});
