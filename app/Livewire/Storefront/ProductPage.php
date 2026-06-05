<?php

namespace App\Livewire\Storefront;

use App\Models\Finish;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Support\BasketResolver;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class ProductPage extends Component
{
    public Product $product;

    /** @var array<int, int> attribute_id => attribute_value_id */
    public array $selectedValues = [];

    public ?int $selectedFinishId = null;

    public int $quantity = 1;

    public function mount(Product $product): void
    {
        abort_unless($product->is_active, 404);

        $this->product = $product->load([
            'categories.parent',
            'attributes' => fn ($q) => $q->orderBy('sort_order'),
            'attributes.values' => fn ($q) => $q->orderBy('sort_order'),
            'variants' => fn ($q) => $q->where('is_active', true),
            'variants.attributeValues.attribute',
            'finishes',
            'trustBadges',
            'media',
        ]);

        foreach ($this->product->attributes as $attribute) {
            $first = $attribute->values->first();

            if ($first) {
                $this->selectedValues[$attribute->id] = $first->id;
            }
        }

        $this->selectedFinishId = $this->product->finishes->first()?->id;
    }

    #[Computed]
    public function selectedVariant(): ?ProductVariant
    {
        if ($this->product->attributes->isEmpty()) {
            return $this->product->variants->first();
        }

        $target = collect($this->selectedValues)->map(fn ($v) => (int) $v)->sort()->values()->all();

        if (count($target) !== $this->product->attributes->count()) {
            return null;
        }

        return $this->product->variants->first(function (ProductVariant $variant) use ($target) {
            $variantValueIds = $variant->attributeValues->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();

            return $variantValueIds === $target;
        });
    }

    #[Computed]
    public function priceRange(): string
    {
        $prices = $this->product->variants->pluck('price')->map(fn ($p) => (float) $p);

        if ($prices->isEmpty()) {
            return '£'.number_format((float) $this->product->base_price, 2);
        }

        $min = $prices->min();
        $max = $prices->max();

        return $min === $max
            ? '£'.number_format($min, 2)
            : '£'.number_format($min, 2).' – £'.number_format($max, 2);
    }

    #[Computed]
    public function displayPrice(): string
    {
        $variant = $this->selectedVariant;

        return $variant
            ? '£'.number_format((float) $variant->price, 2)
            : $this->priceRange;
    }

    #[Computed]
    public function relatedProducts(): Collection
    {
        $categoryIds = $this->product->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $this->product->id)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->orderBy('sort_order')
            ->limit(4)
            ->get();
    }

    public function selectValue(int $attributeId, int $valueId): void
    {
        $this->selectedValues[$attributeId] = $valueId;
    }

    public function selectFinish(int $finishId): void
    {
        $valid = $this->product->finishes->pluck('id')->contains($finishId);

        if ($valid) {
            $this->selectedFinishId = $finishId;
        }
    }

    public function increment(): void
    {
        $this->quantity = min(99, $this->quantity + 1);
    }

    public function decrement(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function addToBasket(BasketResolver $resolver): void
    {
        $variant = $this->selectedVariant;

        if (! $variant) {
            Flux::toast(variant: 'danger', text: __('Please choose every option before adding to basket.'));

            return;
        }

        $finishId = null;

        if ($this->product->finishes->isNotEmpty()) {
            if (! $this->selectedFinishId) {
                Flux::toast(variant: 'danger', text: __('Please choose a finish.'));

                return;
            }

            $finishId = Finish::query()
                ->whereIn('id', $this->product->finishes->pluck('id'))
                ->where('id', $this->selectedFinishId)
                ->value('id');
        }

        $quantity = max(1, min(99, (int) $this->quantity));

        $resolver->current()->addItem($variant, [], $quantity, $finishId);

        $this->dispatch('basket-updated');

        Flux::toast(variant: 'success', text: __('Added to basket.'));

        $this->quantity = 1;
    }

    public function render(): View
    {
        return view('livewire.storefront.product-page');
    }
}
