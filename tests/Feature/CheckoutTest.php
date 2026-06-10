<?php

use App\Livewire\Storefront\CheckoutPage;
use App\Models\Basket;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductVariant;
use App\Support\BasketResolver;

use function Pest\Livewire\livewire;

function freshBasketWithItem(): Basket
{
    $product = Product::factory()->create(['name' => 'Oak Console Table']);
    $variant = ProductVariant::factory()->for($product)->create(['price' => 199.00]);
    $addon = ProductAddon::factory()->for($product)->create(['price' => 15.00]);

    $basket = Basket::factory()->create();
    $basket->addItem($variant, [$addon->id], 2);

    return $basket->fresh();
}

function bindBasketCookie(Basket $basket): void
{
    $resolver = new class($basket) extends BasketResolver
    {
        public function __construct(private Basket $basket) {}

        public function current(): Basket
        {
            return $this->basket;
        }
    };

    app()->instance(BasketResolver::class, $resolver);
}

it('redirects to the basket page when the basket is empty', function () {
    $basket = Basket::factory()->create();

    $this->withCookie('basket_token', $basket->token)
        ->get('/checkout')
        ->assertRedirect(route('basket.show'));
});

it('renders the checkout page when the basket has items', function () {
    $basket = freshBasketWithItem();

    $this->withCookie('basket_token', $basket->token)
        ->get('/checkout')
        ->assertOk()
        ->assertSeeLivewire(CheckoutPage::class);
});

it('shows each item and the subtotal in the order summary', function () {
    $basket = freshBasketWithItem();

    $response = $this->withCookie('basket_token', $basket->token)->get('/checkout');

    $response->assertOk()
        ->assertSee('Oak Console Table')
        ->assertSee('428.00');
});

it('persists the email on blur', function () {
    $basket = freshBasketWithItem();

    bindBasketCookie($basket);

    livewire(CheckoutPage::class)
        ->set('email', 'customer@example.com')
        ->assertHasNoErrors('email');

    expect($basket->fresh()->email)->toBe('customer@example.com');
});

it('persists each detail field on blur', function (string $property, string $column, string $value) {
    $basket = freshBasketWithItem();

    bindBasketCookie($basket);

    livewire(CheckoutPage::class)
        ->set($property, $value)
        ->assertHasNoErrors($property);

    expect($basket->fresh()->{$column})->toBe($value);
})->with([
    'first name' => ['firstName', 'first_name', 'Jane'],
    'last name' => ['lastName', 'last_name', 'Doe'],
    'address line 1' => ['addressLine1', 'address_line_1', '12 Oak Lane'],
    'address line 2' => ['addressLine2', 'address_line_2', 'Apt 4'],
    'city' => ['city', 'city', 'Sheffield'],
    'country' => ['country', 'country', 'US'],
    'state' => ['state', 'state', 'Yorkshire'],
    'postal code' => ['postalCode', 'postal_code', 'S1 2AB'],
]);

it('validates the email format on blur', function () {
    $basket = freshBasketWithItem();

    bindBasketCookie($basket);

    livewire(CheckoutPage::class)
        ->set('email', 'not-an-email')
        ->assertHasErrors(['email' => 'email']);

    expect($basket->fresh()->email)->toBeNull();
});

it('flags required fields when emptied on blur', function (string $property, string $column) {
    $basket = freshBasketWithItem();

    bindBasketCookie($basket);

    livewire(CheckoutPage::class)
        ->set($property, '')
        ->assertHasErrors([$property => 'required']);

    expect($basket->fresh()->getAttribute($column))->toBeNull();
})->with([
    ['firstName', 'first_name'],
    ['lastName', 'last_name'],
    ['addressLine1', 'address_line_1'],
    ['city', 'city'],
    ['postalCode', 'postal_code'],
]);

it('allows address line 2 and state to be empty', function () {
    $basket = freshBasketWithItem();

    bindBasketCookie($basket);

    livewire(CheckoutPage::class)
        ->set('addressLine2', '')
        ->set('state', '')
        ->assertHasNoErrors(['addressLine2', 'state']);
});

it('rejects an unknown country code', function () {
    $basket = freshBasketWithItem();

    bindBasketCookie($basket);

    livewire(CheckoutPage::class)
        ->set('country', 'ZZ')
        ->assertHasErrors('country');
});

it('hydrates form fields from the basket on mount', function () {
    $basket = freshBasketWithItem();
    $basket->update([
        'email' => 'jane@example.com',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'address_line_1' => '12 Oak Lane',
        'city' => 'Sheffield',
        'country' => 'GB',
        'postal_code' => 'S1 2AB',
    ]);

    bindBasketCookie($basket);

    livewire(CheckoutPage::class)
        ->assertSet('email', 'jane@example.com')
        ->assertSet('firstName', 'Jane')
        ->assertSet('lastName', 'Doe')
        ->assertSet('addressLine1', '12 Oak Lane')
        ->assertSet('city', 'Sheffield')
        ->assertSet('country', 'GB')
        ->assertSet('postalCode', 'S1 2AB');
});

it('renders the numbered step labels for each section', function () {
    $basket = freshBasketWithItem();

    bindBasketCookie($basket);

    livewire(CheckoutPage::class)
        ->assertSee('Step 1 of 3')
        ->assertSee('Step 2 of 3')
        ->assertSee('Step 3 of 3')
        ->assertSee('Contact Information')
        ->assertSee('Shipping Address')
        ->assertSee('Payment');
});

it('renders the order summary and complete-purchase CTA', function () {
    $basket = freshBasketWithItem();

    bindBasketCookie($basket);

    livewire(CheckoutPage::class)
        ->assertSee('Order Summary')
        ->assertSee('Complete Purchase')
        ->assertSee('Eco-Friendly Packaging')
        ->assertSee('Hand-Checked Quality');
});

it('shows the checkout link on the basket page when it has items', function () {
    $basket = freshBasketWithItem();

    $this->withCookie('basket_token', $basket->token)
        ->get('/basket')
        ->assertOk()
        ->assertSee(route('checkout.show'));
});

it('hides the checkout link when the basket is empty', function () {
    $basket = Basket::factory()->create();

    $this->withCookie('basket_token', $basket->token)
        ->get('/basket')
        ->assertOk()
        ->assertDontSee(route('checkout.show'));
});
