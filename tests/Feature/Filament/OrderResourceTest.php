<?php

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Orders\RelationManagers\PaymentsRelationManager;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists orders', function () {
    $orders = Order::factory()->count(3)->create();

    livewire(ListOrders::class)
        ->assertCanSeeTableRecords($orders);
});

it('filters orders by status', function () {
    $paid = Order::factory()->paid()->create();
    $pending = Order::factory()->create();

    livewire(ListOrders::class)
        ->filterTable('status', [OrderStatus::Paid->value])
        ->assertCanSeeTableRecords([$paid])
        ->assertCanNotSeeTableRecords([$pending]);
});

it('allows editing the shipping address', function () {
    $order = Order::factory()->create([
        'address_line_1' => '1 Old Lane',
        'city' => 'Oldtown',
    ]);

    livewire(EditOrder::class, ['record' => $order->getRouteKey()])
        ->fillForm([
            'address_line_1' => '99 New Road',
            'city' => 'Newtown',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $order->refresh();
    expect($order->address_line_1)->toBe('99 New Road')
        ->and($order->city)->toBe('Newtown');
});

it('persists internal notes', function () {
    $order = Order::factory()->create();

    livewire(EditOrder::class, ['record' => $order->getRouteKey()])
        ->fillForm(['internal_notes' => 'Customer wants gift wrap.'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($order->fresh()->internal_notes)->toBe('Customer wants gift wrap.');
});

it('disables read-only customer fields on the edit page', function () {
    $order = Order::factory()->create();

    livewire(EditOrder::class, ['record' => $order->getRouteKey()])
        ->assertFormFieldIsDisabled('email')
        ->assertFormFieldIsDisabled('first_name')
        ->assertFormFieldIsDisabled('last_name');
});

it('cancels a pending order via the header action', function () {
    $order = Order::factory()->create();

    livewire(EditOrder::class, ['record' => $order->getRouteKey()])
        ->callAction('cancel');

    $order->refresh();
    expect($order->status)->toBe(OrderStatus::Cancelled)
        ->and($order->cancelled_at)->not->toBeNull();
});

it('hides the cancel action for terminal orders', function () {
    $order = Order::factory()->paid()->create();

    livewire(EditOrder::class, ['record' => $order->getRouteKey()])
        ->assertActionHidden('cancel');
});

it('lists order items in the relation manager', function () {
    $order = Order::factory()->create();
    $items = OrderItem::factory()->count(2)->for($order)->create();

    livewire(ItemsRelationManager::class, [
        'ownerRecord' => $order,
        'pageClass' => EditOrder::class,
    ])->assertCanSeeTableRecords($items);
});

it('lists payments in the relation manager', function () {
    $order = Order::factory()->create();
    $payments = Payment::factory()->count(2)->for($order)->create();

    livewire(PaymentsRelationManager::class, [
        'ownerRecord' => $order,
        'pageClass' => EditOrder::class,
    ])->assertCanSeeTableRecords($payments);
});
