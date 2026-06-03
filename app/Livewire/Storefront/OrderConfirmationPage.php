<?php

namespace App\Livewire\Storefront;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Support\BasketResolver;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.storefront')]
#[Title('Order confirmation')]
class OrderConfirmationPage extends Component
{
    public Order $order;

    public bool $cookieCleared = false;

    public function mount(Order $order): void
    {
        $this->order = $order->load('items.addons');
    }

    public function refreshStatus(BasketResolver $resolver): void
    {
        $this->order = $this->order->fresh(['items.addons']);

        if ($this->order->isPaid() && ! $this->cookieCleared) {
            Cookie::queue(Cookie::forget('basket_token'));
            $resolver->forget();
            $this->cookieCleared = true;
        }
    }

    public function isTerminal(): bool
    {
        return $this->order->status->isTerminal();
    }

    public function render()
    {
        return view('livewire.storefront.order-confirmation-page', [
            'awaiting' => $this->order->status === OrderStatus::AwaitingPayment
                || $this->order->status === OrderStatus::Pending,
            'failed' => $this->order->status === OrderStatus::Failed,
            'cancelled' => $this->order->status === OrderStatus::Cancelled,
            'paid' => $this->order->isPaid(),
        ]);
    }
}
