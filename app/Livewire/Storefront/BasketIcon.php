<?php

namespace App\Livewire\Storefront;

use App\Support\BasketResolver;
use Livewire\Attributes\On;
use Livewire\Component;

class BasketIcon extends Component
{
    public int $count = 0;

    public function mount(BasketResolver $resolver): void
    {
        $this->refreshCount($resolver);
    }

    #[On('basket-updated')]
    public function refreshCount(BasketResolver $resolver): void
    {
        $this->count = $resolver->current()->itemCount();
    }

    public function render()
    {
        return view('livewire.storefront.basket-icon');
    }
}
