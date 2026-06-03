<?php

namespace App\Payments\Contracts;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\Exceptions\PaymentException;
use Illuminate\Http\Request;

interface PaymentGateway
{
    /**
     * Short identifier, e.g. "stripe", "paypal".
     */
    public function key(): string;

    /**
     * Human-friendly label.
     */
    public function displayName(): string;

    /**
     * Create an upstream payment intent for an order, persist the
     * matching Payment row, and return the data the frontend needs
     * to complete payment.
     */
    public function createIntent(Order $order): PaymentIntentResult;

    /**
     * Verify the inbound webhook request and return a normalized event.
     *
     * @throws PaymentException on bad signature
     */
    public function verifyWebhook(Request $request): WebhookEvent;

    /**
     * Idempotently apply a verified webhook event to internal state.
     */
    public function handleEvent(WebhookEvent $event): void;

    /**
     * Refund a captured payment (full or partial).
     */
    public function refund(Payment $payment, ?int $amount = null): Payment;
}
