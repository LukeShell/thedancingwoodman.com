<?php

use App\Enums\OrderStatus;
use App\Livewire\Storefront\OrderConfirmationPage;
use App\Models\Order;
use Livewire\Livewire;

it('shows the awaiting state while the order is unpaid', function () {
    $order = Order::factory()->awaitingPayment()->create();

    Livewire::test(OrderConfirmationPage::class, ['order' => $order])
        ->assertSee('Confirming your payment')
        ->assertSee($order->reference);
});

it('flips to the paid state after refreshStatus observes payment', function () {
    $order = Order::factory()->awaitingPayment()->create();

    $component = Livewire::test(OrderConfirmationPage::class, ['order' => $order])
        ->assertSee('Confirming your payment');

    $order->forceFill(['status' => OrderStatus::Paid, 'paid_at' => now()])->save();

    $component->call('refreshStatus')
        ->assertSee('Thank you for your order');
});

it('shows the failure UI when status is failed', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Failed]);

    Livewire::test(OrderConfirmationPage::class, ['order' => $order])
        ->assertSee('Payment failed');
});
