<?php

namespace App\Payments\Gateways;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGateway;
use App\Payments\Contracts\PaymentIntentResult;
use App\Payments\Contracts\WebhookEvent;
use App\Payments\Exceptions\PaymentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeGateway implements PaymentGateway
{
    private ?StripeClient $client = null;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(private readonly array $config) {}

    public function key(): string
    {
        return 'stripe';
    }

    public function displayName(): string
    {
        return 'Stripe';
    }

    public function createIntent(Order $order): PaymentIntentResult
    {
        $intent = $this->client()->paymentIntents->create([
            'amount' => $order->grand_total,
            'currency' => strtolower($order->currency),
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_reference' => $order->reference,
            ],
            'receipt_email' => $order->email,
            'automatic_payment_methods' => [
                'enabled' => (bool) ($this->config['automatic_payment_methods'] ?? true),
            ],
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => $this->key(),
            'gateway_payment_id' => $intent->id,
            'status' => PaymentStatus::Processing,
            'amount' => $order->grand_total,
            'currency' => $order->currency,
            'client_secret' => $intent->client_secret,
            'metadata' => ['intent_status' => $intent->status],
        ]);

        $order->update(['status' => OrderStatus::AwaitingPayment]);

        return new PaymentIntentResult(
            clientSecret: (string) $intent->client_secret,
            publishableKey: (string) ($this->config['publishable_key'] ?? ''),
            paymentId: $payment->id,
            gatewayPaymentId: $intent->id,
        );
    }

    public function verifyWebhook(Request $request): WebhookEvent
    {
        $secret = (string) ($this->config['webhook_secret'] ?? '');
        $signature = (string) $request->header('Stripe-Signature', '');

        if ($secret === '') {
            throw new PaymentException('Stripe webhook secret is not configured.');
        }

        try {
            $event = Webhook::constructEvent($request->getContent(), $signature, $secret);
        } catch (SignatureVerificationException $e) {
            throw new PaymentException('Invalid Stripe webhook signature: '.$e->getMessage(), previous: $e);
        }

        /** @var array<string, mixed> $payload */
        $payload = $event->toArray();

        return new WebhookEvent(
            id: (string) $event->id,
            type: (string) $event->type,
            payload: $payload,
        );
    }

    public function handleEvent(WebhookEvent $event): void
    {
        $object = $event->payload['data']['object'] ?? [];
        $paymentIntentId = is_array($object) ? ($object['id'] ?? null) : null;

        if (! is_string($paymentIntentId)) {
            Log::warning('Stripe webhook received without payment intent id', ['type' => $event->type]);

            return;
        }

        $payment = Payment::query()
            ->where('gateway', $this->key())
            ->where('gateway_payment_id', $paymentIntentId)
            ->first();

        if (! $payment) {
            Log::warning('Stripe webhook for unknown payment', ['intent' => $paymentIntentId, 'type' => $event->type]);

            return;
        }

        match ($event->type) {
            'payment_intent.succeeded' => $this->markSucceeded($payment, $object),
            'payment_intent.processing' => $this->markProcessing($payment, $object),
            'payment_intent.payment_failed' => $this->markFailed($payment, $object),
            'payment_intent.canceled' => $this->markCancelled($payment, $object),
            'charge.refunded' => $this->markRefunded($payment, $object),
            default => null,
        };
    }

    public function refund(Payment $payment, ?int $amount = null): Payment
    {
        throw new PaymentException('Stripe refunds are not yet implemented.');
    }

    /**
     * @param  array<string, mixed>  $intent
     */
    private function markSucceeded(Payment $payment, array $intent): void
    {
        $payment->forceFill([
            'status' => PaymentStatus::Succeeded,
            'payment_method_type' => $this->extractMethodType($intent),
            'metadata' => array_merge((array) $payment->metadata, ['intent_status' => 'succeeded']),
            'processed_at' => now(),
            'last_error' => null,
        ])->save();

        $order = $payment->order;
        if ($order && ! $order->isPaid()) {
            $order->forceFill([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
            ])->save();

            OrderPaid::dispatch($order->fresh());
        }
    }

    /**
     * @param  array<string, mixed>  $intent
     */
    private function markProcessing(Payment $payment, array $intent): void
    {
        $payment->forceFill([
            'status' => PaymentStatus::Processing,
            'payment_method_type' => $this->extractMethodType($intent),
            'metadata' => array_merge((array) $payment->metadata, ['intent_status' => 'processing']),
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $intent
     */
    private function markFailed(Payment $payment, array $intent): void
    {
        $lastError = $intent['last_payment_error']['message'] ?? null;

        $payment->forceFill([
            'status' => PaymentStatus::Failed,
            'last_error' => is_string($lastError) ? $lastError : null,
            'metadata' => array_merge((array) $payment->metadata, ['intent_status' => 'failed']),
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $intent
     */
    private function markCancelled(Payment $payment, array $intent): void
    {
        $payment->forceFill([
            'status' => PaymentStatus::Cancelled,
            'metadata' => array_merge((array) $payment->metadata, ['intent_status' => 'canceled']),
        ])->save();
    }

    /**
     * @param  array<string, mixed>  $charge
     */
    private function markRefunded(Payment $payment, array $charge): void
    {
        $payment->forceFill([
            'status' => PaymentStatus::Refunded,
            'metadata' => array_merge((array) $payment->metadata, ['intent_status' => 'refunded']),
        ])->save();

        $order = $payment->order;
        if ($order && $order->status !== OrderStatus::Refunded) {
            $order->forceFill(['status' => OrderStatus::Refunded])->save();
        }
    }

    /**
     * @param  array<string, mixed>  $intent
     */
    private function extractMethodType(array $intent): ?string
    {
        $types = $intent['payment_method_types'] ?? null;

        if (is_array($types) && isset($types[0]) && is_string($types[0])) {
            return $types[0];
        }

        return null;
    }

    private function client(): StripeClient
    {
        if ($this->client === null) {
            $secret = (string) ($this->config['secret_key'] ?? '');

            if ($secret === '') {
                throw new PaymentException('Stripe secret key is not configured.');
            }

            $this->client = new StripeClient($secret);
        }

        return $this->client;
    }
}
