<div class="space-y-8">
    @if ($product->variants->isEmpty())
        <p class="text-body-md text-on-surface-variant">
            {{ __('No purchasable variants available right now.') }}
        </p>
    @else
        <div>
            <p class="mb-3 text-label-sm uppercase text-on-surface-variant">
                {{ __('Choose variant') }}
            </p>
            <div class="space-y-2">
                @foreach ($product->variants as $variant)
                    <label class="flex cursor-pointer items-center justify-between gap-4 border border-outline-variant/60 bg-surface-container-lowest px-4 py-3 text-body-md transition hover:border-oak-deep"
                           wire:key="variant-{{ $variant->id }}">
                        <span class="flex items-center gap-3">
                            <input type="radio" wire:model.live="selectedVariantId" value="{{ $variant->id }}"
                                   class="h-4 w-4 border-timber-ash text-oak-deep focus:ring-oak-deep" />
                            <span>
                                <span class="font-medium text-on-surface">
                                    {{ $variant->attributeValues->map(fn ($v) => $v->attribute->name . ': ' . $v->value)->implode(' / ') ?: $variant->sku }}
                                </span>
                                @if ($variant->sku)
                                    <span class="ml-2 font-mono text-body-sm text-on-surface-variant">{{ $variant->sku }}</span>
                                @endif
                            </span>
                        </span>
                        <span class="font-semibold text-oak-deep">
                            <x-storefront.price :amount="$variant->price" />
                        </span>
                    </label>
                @endforeach
            </div>
            <x-storefront.field-error name="selectedVariantId" class="mt-2" />
        </div>

        @if ($product->addons->isNotEmpty())
            <div>
                <p class="mb-3 text-label-sm uppercase text-on-surface-variant">
                    {{ __('Optional add-ons') }}
                </p>
                <div class="space-y-2">
                    @foreach ($product->addons as $addon)
                        <label class="flex cursor-pointer items-start justify-between gap-4 border border-outline-variant/40 bg-surface-container-lowest px-4 py-3 text-body-md"
                               wire:key="addon-{{ $addon->id }}">
                            <span class="flex items-start gap-3">
                                <input type="checkbox" wire:model.live="selectedAddonIds" value="{{ $addon->id }}"
                                       class="mt-0.5 h-4 w-4 rounded-sm border-timber-ash text-oak-deep focus:ring-oak-deep" />
                                <span>
                                    <span class="font-medium text-on-surface">{{ $addon->name }}</span>
                                    @if ($addon->description)
                                        <span class="block text-body-sm text-on-surface-variant">{{ $addon->description }}</span>
                                    @endif
                                </span>
                            </span>
                            <span class="shrink-0 text-on-surface">+<x-storefront.price :amount="$addon->price" /></span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex items-end gap-4">
            <x-storefront.quantity-input
                id="qty-{{ $product->id }}"
                wire:model="quantity"
                :label="__('Quantity')"
            />
            <span class="text-label-md uppercase text-on-surface-variant">{{ __('Quantity') }}</span>
        </div>
        <x-storefront.field-error name="quantity" />

        <x-storefront.button
            type="button"
            variant="primary"
            size="lg"
            wire:click="addToBasket"
            wire:loading.attr="disabled"
            class="w-full"
        >
            <span wire:loading.remove wire:target="addToBasket">{{ __('Add to basket') }}</span>
            <span wire:loading wire:target="addToBasket">{{ __('Adding...') }}</span>
        </x-storefront.button>
    @endif
</div>
