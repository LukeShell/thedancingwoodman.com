<div>
    <x-storefront.section>
        <x-storefront.eyebrow>{{ __('Basket') }}</x-storefront.eyebrow>
        <x-storefront.heading :level="1" class="mt-3 mb-10">
            {{ __('Your basket') }}
        </x-storefront.heading>

        @if ($items->isEmpty())
            <x-storefront.empty-state :message="__('Your basket is empty.')">
                <x-storefront.button :href="route('shop.index')" variant="primary">
                    {{ __('Browse the shop') }}
                </x-storefront.button>
            </x-storefront.empty-state>
        @else
            <div class="bg-surface-container-lowest">
                <ul class="divide-y divide-outline-variant/40">
                    @foreach ($items as $item)
                        <li class="flex flex-col gap-4 p-6 sm:flex-row sm:items-start" wire:key="item-{{ $item->id }}">
                            <div class="h-24 w-24 shrink-0 overflow-hidden bg-surface-container">
                                @if ($img = $item->variant->product->primaryImage())
                                    <img src="{{ $img->getUrl() }}" alt="{{ $item->variant->product->name }}" class="h-full w-full object-cover" />
                                @endif
                            </div>

                            <div class="flex-1">
                                <a href="{{ route('shop.show', $item->variant->product) }}"
                                   class="font-display text-headline-sm text-oak-deep hover:underline">
                                    {{ $item->variant->product->name }}
                                </a>
                                <p class="mt-1 text-body-sm text-on-surface-variant">
                                    {{ $item->variant->attributeValues->map(fn ($v) => $v->attribute->name . ': ' . $v->value)->implode(' / ') ?: $item->variant->sku }}
                                </p>
                                @if ($item->addons->isNotEmpty())
                                    <ul class="mt-2 text-body-sm text-on-surface-variant">
                                        @foreach ($item->addons as $addon)
                                            <li>+ {{ $addon->name }} (<x-storefront.price :amount="$addon->price" />)</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <p class="mt-3 text-body-md text-on-surface">
                                    <x-storefront.price :amount="$item->unitPrice()" /> {{ __('each') }}
                                </p>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <x-storefront.quantity-input
                                    id="qty-{{ $item->id }}"
                                    :value="$item->quantity"
                                    wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                    :label="__('Quantity')"
                                />
                                <button type="button" wire:click="removeItem({{ $item->id }})"
                                        class="text-label-sm uppercase text-on-surface-variant hover:text-brand-accent">
                                    {{ __('Remove') }}
                                </button>
                            </div>

                            <div class="text-right sm:w-32">
                                <p class="text-label-sm uppercase text-on-surface-variant">{{ __('Line total') }}</p>
                                <p class="mt-1 text-headline-sm text-oak-deep">
                                    <x-storefront.price :amount="$item->lineTotal()" />
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="flex flex-col gap-4 border-t border-outline-variant/40 bg-surface-container-low px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-label-sm uppercase text-on-surface-variant">{{ __('Subtotal') }}</span>
                        <span class="text-headline-md text-oak-deep">
                            <x-storefront.price :amount="$subtotal" />
                        </span>
                    </div>

                    <x-storefront.button :href="route('checkout.show')" variant="primary" size="lg" wire:navigate>
                        {{ __('Proceed to checkout') }}
                    </x-storefront.button>
                </div>
            </div>
        @endif
    </x-storefront.section>
</div>
