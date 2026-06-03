<?php

use App\Payments\Exceptions\PaymentException;
use App\Payments\Gateways\StripeGateway;
use App\Payments\PaymentManager;
use Tests\Support\FakePaymentGateway;

it('resolves the default gateway', function () {
    $manager = app(PaymentManager::class);

    expect($manager->driver())->toBeInstanceOf(StripeGateway::class)
        ->and($manager->driver()->key())->toBe('stripe');
});

it('throws when an unknown gateway is requested', function () {
    app(PaymentManager::class)->driver('does-not-exist');
})->throws(PaymentException::class);

it('does not register disabled gateways', function () {
    config()->set('payments.gateways.stripe.enabled', false);
    $this->app->forgetInstance(PaymentManager::class);

    expect(app(PaymentManager::class)->available())->toBeEmpty();
});

it('lists registered gateways', function () {
    $manager = new PaymentManager('stripe');
    $manager->register(new FakePaymentGateway);

    expect($manager->available())->toHaveKey('stripe');
});
