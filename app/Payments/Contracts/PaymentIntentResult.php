<?php

namespace App\Payments\Contracts;

readonly class PaymentIntentResult
{
    public function __construct(
        public string $clientSecret,
        public string $publishableKey,
        public int $paymentId,
        public string $gatewayPaymentId,
    ) {}
}
