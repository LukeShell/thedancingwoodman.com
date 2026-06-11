<div>
    <style>
        .basket-grain {
            background-image: url('https://www.transparenttextures.com/patterns/natural-paper.png');
            opacity: 0.03;
            pointer-events: none;
        }
    </style>

    <x-storefront.section class="relative">
        <div class="basket-grain absolute inset-0"></div>

        <div class="relative z-10">
            <h1 class="mb-4 font-display text-3xl text-oak-deep sm:text-headline-xl">{{ __('Your Basket') }}</h1>
            <p class="mb-12 max-w-2xl text-secondary">
                {{ __('Items handcrafted specifically for your order. Review your selection of artisanal woodwares before proceeding to the workshop queue.') }}
            </p>

            @if ($items->isEmpty())
                <x-storefront.empty-state :message="__('Your basket is empty.')">
                    <x-storefront.button :href="route('shop.index')" variant="primary" wire:navigate>
                        {{ __('Browse the shop') }}
                    </x-storefront.button>
                </x-storefront.empty-state>
            @else
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
                    {{-- Items column --}}
                    <div class="lg:col-span-8">
                        <div class="mb-8 hidden grid-cols-12 gap-4 border-b border-timber-ash/20 pb-4 md:grid">
                            <div class="col-span-6 text-label-sm uppercase tracking-wider text-timber-ash">
                                {{ __('Product Details') }}
                            </div>
                            <div class="col-span-2 text-center text-label-sm uppercase tracking-wider text-timber-ash">
                                {{ __('Quantity') }}
                            </div>
                            <div class="col-span-2 text-right text-label-sm uppercase tracking-wider text-timber-ash">
                                {{ __('Price') }}
                            </div>
                            <div class="col-span-2 text-right text-label-sm uppercase tracking-wider text-timber-ash">
                                {{ __('Total') }}
                            </div>
                        </div>

                        @foreach ($items as $item)
                            <div
                                wire:key="item-{{ $item->id }}"
                                class="group grid grid-cols-1 items-center gap-6 border-b border-timber-ash/10 py-8 md:grid-cols-12"
                            >
                                <div class="flex gap-6 md:col-span-6">
                                    <div class="h-32 w-24 shrink-0 overflow-hidden bg-surface-container md:h-40 md:w-32">
                                        @if ($item->cardImageUrl())
                                            <img
                                                src="{{ $item->cardImageUrl() }}"
                                                alt="{{ $item->variant->product->name }}"
                                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                            />
                                        @endif
                                    </div>

                                    <div class="flex flex-col justify-center">
                                        <a
                                            href="{{ route('shop.show', $item->variant->product) }}"
                                            wire:navigate
                                            class="mb-1 font-display text-headline-md text-oak-deep hover:underline"
                                        >
                                            {{ $item->variant->product->name }}
                                        </a>

                                        @foreach ($item->variantLines() as $line)
                                            <p class="mb-2 text-label-md text-secondary">{{ $line }}</p>
                                        @endforeach

                                        @if ($item->finish)
                                            <p class="mb-2 text-label-md text-secondary">
                                                {{ __('Finish') }}: {{ $item->finish->name }}
                                            </p>
                                        @endif

                                        @foreach ($item->addons as $addon)
                                            <p class="mb-2 text-label-md text-secondary">
                                                + {{ $addon->name }}
                                                (<x-storefront.price :amount="$addon->price" />)
                                            </p>
                                        @endforeach

                                        <button
                                            type="button"
                                            wire:click="removeItem({{ $item->id }})"
                                            class="mt-2 flex items-center gap-1 self-start text-label-sm text-timber-ash transition-colors hover:text-brand-accent"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                            {{ __('Remove') }}
                                        </button>
                                    </div>
                                </div>

                                <div class="flex justify-center md:col-span-2">
                                    <x-storefront.quantity-stepper
                                        :value="$item->quantity"
                                        :decrement="'decrementItem('.$item->id.')'"
                                        :increment="'incrementItem('.$item->id.')'"
                                        :label="__('Quantity for :name', ['name' => $item->variant->product->name])"
                                        class="h-10"
                                    />
                                </div>

                                <div class="hidden text-right md:col-span-2 md:block">
                                    <x-storefront.price :amount="$item->unitPrice()" class="font-medium text-oak-deep" />
                                </div>

                                <div class="text-right md:col-span-2">
                                    <x-storefront.price
                                        :amount="$item->lineTotal()"
                                        class="text-lg font-bold text-oak-deep md:text-base"
                                    />
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-8">
                            <a
                                href="{{ route('shop.index') }}"
                                wire:navigate
                                class="inline-flex items-center gap-2 font-medium text-oak-deep transition-transform hover:-translate-x-1"
                            >
                                <span class="material-symbols-outlined">arrow_back</span>
                                {{ __('Continue Shopping') }}
                            </a>
                        </div>
                    </div>

                    {{-- Order summary sidebar --}}
                    <div class="lg:col-span-4">
                        <div class="sticky top-32 border border-timber-ash/10 bg-sapwood-cream p-8">
                            <h2 class="mb-8 font-display text-headline-md text-oak-deep">{{ __('Order Summary') }}</h2>

                            <div class="mb-8 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-secondary">{{ __('Subtotal') }}</span>
                                    <x-storefront.price :amount="$subtotal" class="font-medium text-oak-deep" />
                                </div>
                                @if ($discount)
                                    <div class="flex items-center justify-between">
                                        <span class="text-secondary">
                                            {{ __('Discount') }}
                                            <span class="ml-1 text-label-sm uppercase tracking-wider text-timber-ash">({{ $discount->code }})</span>
                                        </span>
                                        <span class="font-medium text-brand-accent">
                                            -<x-storefront.price :amount="$discountAmount" />
                                        </span>
                                    </div>
                                @endif
                                <div class="flex items-start justify-between gap-4">
                                    <span class="text-secondary">{{ __('Shipping') }}</span>
                                    <span class="text-right text-label-sm uppercase tracking-wider text-timber-ash">
                                        {{ __('Calculated at checkout') }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-8 border-t border-timber-ash/20 pt-6">
                                <div class="flex items-end justify-between">
                                    <span class="font-display text-headline-md text-oak-deep">{{ __('Total') }}</span>
                                    <x-storefront.price :amount="$total" class="text-2xl font-bold text-oak-deep" />
                                </div>
                            </div>

                            <div class="mb-8">
                                <label for="promo-code" class="mb-2 block text-label-sm uppercase tracking-widest text-timber-ash">
                                    {{ __('Discount Code') }}
                                </label>
                                @if ($discount)
                                    <div class="flex items-center justify-between gap-2 border-b border-timber-ash/30 pb-2">
                                        <span class="text-body-md font-medium text-oak-deep">{{ $discount->code }}</span>
                                        <button
                                            type="button"
                                            wire:click="removePromo"
                                            class="text-label-sm font-bold uppercase tracking-widest text-timber-ash transition-colors hover:text-brand-accent"
                                        >
                                            {{ __('Remove') }}
                                        </button>
                                    </div>
                                @else
                                    <div class="flex gap-2">
                                        <input
                                            id="promo-code"
                                            type="text"
                                            wire:model="promoCode"
                                            wire:keydown.enter="applyPromo"
                                            placeholder="{{ __('Enter code') }}"
                                            class="flex-1 border-0 border-b border-timber-ash/30 bg-white px-0 text-body-md outline-none transition-colors focus:border-oak-deep focus:ring-0"
                                        />
                                        <button
                                            type="button"
                                            wire:click="applyPromo"
                                            class="text-label-sm font-bold uppercase tracking-widest text-oak-deep transition-opacity hover:opacity-70"
                                        >
                                            {{ __('Apply') }}
                                        </button>
                                    </div>
                                @endif
                                @if ($promoError)
                                    <p class="mt-2 text-label-sm text-brand-accent">{{ $promoError }}</p>
                                @endif
                                @if ($promoSuccess)
                                    <p class="mt-2 text-label-sm text-secondary">{{ $promoSuccess }}</p>
                                @endif
                            </div>

                            <x-storefront.button
                                variant="primary"
                                size="lg"
                                :href="route('checkout.show')"
                                wire:navigate
                                class="w-full"
                            >
                                {{ __('Proceed to Checkout') }}
                            </x-storefront.button>

                            <div class="mt-8 flex flex-col gap-4">
                                <div class="flex items-center gap-3 text-label-sm text-secondary">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                    {{ __('Secure checkout guaranteed') }}
                                </div>
                                <div class="flex items-center gap-3 text-label-sm text-secondary">
                                    <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                    {{ __('Hand-delivered by our craftsmen') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-storefront.section>
</div>
