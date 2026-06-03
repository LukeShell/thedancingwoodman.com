<?php

use App\Payments\Gateways\StripeGateway;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | The gateway key that will be used when no explicit gateway is provided
    | at order placement time. Must exist (and be enabled) in the gateways
    | array below.
    |
    */

    'default' => env('PAYMENTS_DEFAULT', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The ISO-4217 currency code stamped on every new order. Amounts are
    | stored in minor units (e.g. pence for GBP).
    |
    */

    'currency' => env('PAYMENTS_CURRENCY', 'GBP'),

    /*
    |--------------------------------------------------------------------------
    | Registered Gateways
    |--------------------------------------------------------------------------
    |
    | Each gateway is keyed by its short identifier. The `driver` class must
    | implement App\Payments\Contracts\PaymentGateway. Disabled gateways are
    | not registered with the PaymentManager.
    |
    */

    'gateways' => [

        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', true),
            'driver' => StripeGateway::class,
            'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'payment_method_types' => ['card'],
            'automatic_payment_methods' => env('STRIPE_AUTOMATIC_PAYMENT_METHODS', true),
        ],

        // 'paypal' => [
        //     'enabled' => env('PAYPAL_ENABLED', false),
        //     'driver' => App\Payments\Gateways\PayPalGateway::class,
        //     ...
        // ],

    ],
];
