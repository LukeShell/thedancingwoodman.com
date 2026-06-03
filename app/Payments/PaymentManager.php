<?php

namespace App\Payments;

use App\Payments\Contracts\PaymentGateway;
use App\Payments\Exceptions\PaymentException;

class PaymentManager
{
    /**
     * @var array<string, PaymentGateway>
     */
    private array $gateways = [];

    public function __construct(private readonly string $defaultGateway) {}

    public function register(PaymentGateway $gateway): void
    {
        $this->gateways[$gateway->key()] = $gateway;
    }

    public function driver(?string $key = null): PaymentGateway
    {
        $key ??= $this->defaultGateway;

        if (! isset($this->gateways[$key])) {
            throw new PaymentException("Payment gateway [{$key}] is not registered.");
        }

        return $this->gateways[$key];
    }

    /**
     * @return array<string, PaymentGateway>
     */
    public function available(): array
    {
        return $this->gateways;
    }

    public function defaultKey(): string
    {
        return $this->defaultGateway;
    }
}
