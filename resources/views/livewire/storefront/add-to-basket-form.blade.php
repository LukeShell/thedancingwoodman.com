<div class="mt-8 space-y-6">
    @if ($product->variants->isEmpty())
        <p class="text-sm text-stone-500 dark:text-stone-400">
            {{ __('No purchasable variants available right now.') }}
        </p>
    @else
        <div>
            <h3 class="mb-2 text-sm font-semibold uppercase tracking-wider text-stone-500">
                {{ __('Choose variant') }}
            </h3>
            <div class="space-y-2">
                @foreach ($product->variants as $variant)
                    <label class="flex cursor-pointer items-center justify-between gap-4 rounded-md border border-stone-300 px-3 py-2 text-sm hover:border-amber-500 dark:border-stone-700 dark:hover:border-amber-500"
                           wire:key="variant-{{ $variant->id }}">
                        <span class="flex items-center gap-3">
                            <input type="radio" wire:model.live="selectedVariantId" value="{{ $variant->id }}"
                                   class="h-4 w-4 border-stone-300 text-amber-600 focus:ring-amber-500" />
                            <span>
                                <span class="font-medium">
                                    {{ $variant->attributeValues->map(fn ($v) => $v->attribute->name . ': ' . $v->value)->implode(' / ') ?: $variant->sku }}
                                </span>
                                @if ($variant->sku)
                                    <span class="ml-2 font-mono text-xs text-stone-500">{{ $variant->sku }}</span>
                                @endif
                            </span>
                        </span>
                        <span class="font-semibold text-amber-700 dark:text-amber-500">&pound;{{ number_format($variant->price, 2) }}</span>
                    </label>
                @endforeach
            </div>
            @error('selectedVariantId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        @if ($product->addons->isNotEmpty())
            <div>
                <h3 class="mb-2 text-sm font-semibold uppercase tracking-wider text-stone-500">
                    {{ __('Optional add-ons') }}
                </h3>
                <div class="space-y-2">
                    @foreach ($product->addons as $addon)
                        <label class="flex cursor-pointer items-start justify-between gap-4 rounded-md border border-stone-200 px-3 py-2 text-sm dark:border-stone-800"
                               wire:key="addon-{{ $addon->id }}">
                            <span class="flex items-start gap-3">
                                <input type="checkbox" wire:model.live="selectedAddonIds" value="{{ $addon->id }}"
                                       class="mt-0.5 h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500" />
                                <span>
                                    <span class="font-medium">{{ $addon->name }}</span>
                                    @if ($addon->description)
                                        <span class="block text-xs text-stone-500 dark:text-stone-400">{{ $addon->description }}</span>
                                    @endif
                                </span>
                            </span>
                            <span class="shrink-0 font-medium">+&pound;{{ number_format($addon->price, 2) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <label for="qty-{{ $product->id }}" class="text-sm font-medium">{{ __('Quantity') }}</label>
            <input id="qty-{{ $product->id }}" type="number" min="1" max="99" wire:model="quantity"
                   class="w-20 rounded-md border-stone-300 px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-900" />
            @error('quantity') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <button type="button" wire:click="addToBasket" wire:loading.attr="disabled"
                class="w-full rounded-md bg-stone-900 px-6 py-3 text-sm font-medium text-white transition hover:bg-stone-800 disabled:opacity-60 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-stone-200">
            <span wire:loading.remove wire:target="addToBasket">{{ __('Add to basket') }}</span>
            <span wire:loading wire:target="addToBasket">{{ __('Adding...') }}</span>
        </button>
    @endif
</div>
