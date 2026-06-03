<?php

namespace Tests\Support;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGateway;
use App\Payments\Contracts\PaymentIntentResult;
use App\Payments\Contracts\WebhookEvent;
use Illuminate\Http\Request;

class FakePaymentGateway implements PaymentGateway
{
    public ?Order $lastOrder = null;

    public int $createIntentCalls = 0;

    public function key(): string
    {
        return 'stripe';
    }

    public function displayName(): string
    {
        return 'Fake Stripe';
    }

    public function createIntent(Order $order): PaymentIntentResult
    {
        $this->createIntentCalls++;
        $this->lastOrder = $order;

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => $this->key(),
            'gateway_payment_id' => 'pi_fake_'.$order->id,
            'status' => PaymentStatus::Processing,
            'amount' => $order->grand_total,
            'currency' => $order->currency,
            'client_secret' => 'pi_fake_'.$order->id.'_secret_test',
            'metadata' => [],
        ]);

        $order->update(['status' => OrderStatus::AwaitingPayment]);

        return new PaymentIntentResult(
            clientSecret: (string) $payment->client_secret,
            publishableKey: 'pk_test_fake',
            paymentId: $payment->id,
            gatewayPaymentId: (string) $payment->gateway_payment_id,
        );
    }

    public function verifyWebhook(Request $request): WebhookEvent
    {
        return new WebhookEvent(
            id: (string) $request->input('id', 'evt_test'),
            type: (string) $request->input('type', 'payment_intent.succeeded'),
            payload: (array) $request->input('data', []),
        );
    }

    public function handleEvent(WebhookEvent $event): void
    {
        // No-op for tests.
    }

    public function refund(Payment $payment, ?int $amount = null): Payment
    {
        return $payment;
    }

    public function simulatePaid(Order $order): void
    {
        $order->forceFill([
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
        ])->save();

        Payment::query()
            ->where('order_id', $order->id)
            ->update([
                'status' => PaymentStatus::Succeeded,
                'processed_at' => now(),
            ]);

        OrderPaid::dispatch($order->fresh());
    }
}
