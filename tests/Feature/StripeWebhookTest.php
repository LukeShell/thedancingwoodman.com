<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\Event;

function signStripePayload(string $payload, string $secret, ?int $timestamp = null): array
{
    $timestamp ??= time();
    $signedPayload = $timestamp.'.'.$payload;
    $signature = hash_hmac('sha256', $signedPayload, $secret);

    return [
        'timestamp' => $timestamp,
        'signature' => "t={$timestamp},v1={$signature}",
    ];
}

beforeEach(function () {
    config()->set('payments.gateways.stripe.webhook_secret', 'whsec_test');
    config()->set('payments.gateways.stripe.secret_key', 'sk_test_dummy');
});

it('marks the order paid on payment_intent.succeeded', function () {
    Event::fake([OrderPaid::class]);

    $order = Order::factory()->awaitingPayment()->create([
        'grand_total' => 5000,
        'currency' => 'GBP',
    ]);
    $payment = Payment::factory()->for($order)->create([
        'gateway' => 'stripe',
        'gateway_payment_id' => 'pi_test_succeed',
        'status' => PaymentStatus::Processing,
        'amount' => 5000,
    ]);

    $payload = json_encode([
        'id' => 'evt_succeed_1',
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => 'pi_test_succeed',
                'payment_method_types' => ['card'],
            ],
        ],
    ]);

    $sig = signStripePayload($payload, 'whsec_test');

    $response = $this->call(
        'POST',
        '/webhooks/payments/stripe',
        [],
        [],
        [],
        ['HTTP_Stripe-Signature' => $sig['signature'], 'CONTENT_TYPE' => 'application/json'],
        $payload,
    );

    $response->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::Paid)
        ->and($order->fresh()->paid_at)->not->toBeNull()
        ->and($payment->fresh()->status)->toBe(PaymentStatus::Succeeded);

    Event::assertDispatched(OrderPaid::class);
});

it('records failed payment without flipping the order to paid', function () {
    $order = Order::factory()->awaitingPayment()->create();
    Payment::factory()->for($order)->create([
        'gateway' => 'stripe',
        'gateway_payment_id' => 'pi_test_failed',
        'status' => PaymentStatus::Processing,
    ]);

    $payload = json_encode([
        'id' => 'evt_fail_1',
        'type' => 'payment_intent.payment_failed',
        'data' => [
            'object' => [
                'id' => 'pi_test_failed',
                'last_payment_error' => ['message' => 'Card declined.'],
            ],
        ],
    ]);

    $sig = signStripePayload($payload, 'whsec_test');

    $this->call('POST', '/webhooks/payments/stripe', [], [], [], [
        'HTTP_Stripe-Signature' => $sig['signature'],
        'CONTENT_TYPE' => 'application/json',
    ], $payload)->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::AwaitingPayment)
        ->and($order->payments()->first()->status)->toBe(PaymentStatus::Failed)
        ->and($order->payments()->first()->last_error)->toBe('Card declined.');
});

it('rejects requests with invalid signatures', function () {
    $payload = json_encode([
        'id' => 'evt_bad',
        'type' => 'payment_intent.succeeded',
        'data' => ['object' => ['id' => 'pi_x']],
    ]);

    $this->call('POST', '/webhooks/payments/stripe', [], [], [], [
        'HTTP_Stripe-Signature' => 't=1,v1=deadbeef',
        'CONTENT_TYPE' => 'application/json',
    ], $payload)->assertStatus(400);

    expect(WebhookEvent::count())->toBe(0);
});

it('is idempotent across duplicate deliveries', function () {
    $order = Order::factory()->awaitingPayment()->create(['grand_total' => 1000]);
    Payment::factory()->for($order)->create([
        'gateway' => 'stripe',
        'gateway_payment_id' => 'pi_dup',
        'status' => PaymentStatus::Processing,
    ]);

    $payload = json_encode([
        'id' => 'evt_dup_1',
        'type' => 'payment_intent.succeeded',
        'data' => ['object' => ['id' => 'pi_dup', 'payment_method_types' => ['card']]],
    ]);
    $sig = signStripePayload($payload, 'whsec_test');

    $headers = [
        'HTTP_Stripe-Signature' => $sig['signature'],
        'CONTENT_TYPE' => 'application/json',
    ];

    $this->call('POST', '/webhooks/payments/stripe', [], [], [], $headers, $payload)->assertOk();
    $second = $this->call('POST', '/webhooks/payments/stripe', [], [], [], $headers, $payload);
    $second->assertOk();
    expect($second->json('status'))->toBe('duplicate')
        ->and(WebhookEvent::count())->toBe(1);
});
