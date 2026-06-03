<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\BasketResolver;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AddToBasketForm extends Component
{
    public Product $product;

    #[Validate('required|integer|exists:product_variants,id')]
    public ?int $selectedVariantId = null;

    /** @var array<int> */
    #[Validate('array')]
    public array $selectedAddonIds = [];

    #[Validate('required|integer|min:1|max:99')]
    public int $quantity = 1;

    public function mount(Product $product): void
    {
        $this->product = $product->load([
            'variants' => fn ($q) => $q->where('is_active', true),
            'variants.attributeValues.attribute',
            'addons' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
        ]);

        $this->selectedVariantId = $this->product->variants->first()?->id;
    }

    public function addToBasket(BasketResolver $resolver): void
    {
        $this->validate();

        $variant = ProductVariant::query()
            ->where('product_id', $this->product->id)
            ->where('is_active', true)
            ->findOrFail($this->selectedVariantId);

        $validAddonIds = $this->product->addons
            ->pluck('id')
            ->intersect($this->selectedAddonIds)
            ->all();

        $resolver->current()->addItem($variant, $validAddonIds, $this->quantity);

        $this->dispatch('basket-updated');

        Flux::toast(variant: 'success', text: __('Added to basket.'));

        $this->reset('selectedAddonIds');
        $this->quantity = 1;
    }

    public function render()
    {
        return view('livewire.storefront.add-to-basket-form');
    }
}
