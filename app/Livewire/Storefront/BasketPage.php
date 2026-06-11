<?php

namespace App\Livewire\Storefront;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Support\BasketResolver;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.storefront')]
#[Title('Your basket')]
class BasketPage extends Component
{
    public Basket $basket;

    public string $promoCode = '';

    public ?string $promoError = null;

    public ?string $promoSuccess = null;

    public function mount(BasketResolver $resolver): void
    {
        $this->basket = $resolver->current();
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = $this->basket->items()->findOrFail($itemId);

        if ($quantity < 1) {
            $item->delete();
        } else {
            $item->update(['quantity' => min(99, $quantity)]);
        }

        $this->dispatch('basket-updated');
    }

    public function incrementItem(int $itemId): void
    {
        $item = $this->basket->items()->findOrFail($itemId);

        $this->updateQuantity($itemId, $item->quantity + 1);
    }

    public function decrementItem(int $itemId): void
    {
        $item = $this->basket->items()->findOrFail($itemId);

        $this->updateQuantity($itemId, $item->quantity - 1);
    }

    public function removeItem(int $itemId): void
    {
        $this->basket->items()->whereKey($itemId)->delete();

        $this->dispatch('basket-updated');
    }

    public function applyPromo(): void
    {
        $this->promoError = null;
        $this->promoSuccess = null;

        if (trim($this->promoCode) === '') {
            $this->promoError = __('Please enter a discount code.');

            return;
        }

        if (! $this->basket->applyCode($this->promoCode)) {
            $this->promoError = __('That code is not valid for your basket.');

            return;
        }

        $this->basket->refresh();
        $this->promoCode = '';
        $this->promoSuccess = __('Discount applied.');
    }

    public function removePromo(): void
    {
        $this->basket->removeDiscount();
        $this->basket->refresh();
        $this->promoCode = '';
        $this->promoError = null;
        $this->promoSuccess = null;
    }

    public function render()
    {
        $items = $this->basket->items()
            ->with([
                'variant.product.media',
                'variant.attributeValues.attribute',
                'addons',
                'finish',
            ])
            ->get();

        $subtotal = $items->sum(fn (BasketItem $item) => (float) $item->lineTotal());
        $discount = $this->basket->discount;
        $discountAmount = $this->basket->discountAmountPence() / 100;
        $total = max(0, $subtotal - $discountAmount);

        return view('livewire.storefront.basket-page', [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'discountAmount' => $discountAmount,
            'total' => $total,
        ]);
    }
}
