<div>
    <x-storefront.section>
        <x-storefront.eyebrow>{{ __('Checkout') }}</x-storefront.eyebrow>
        <x-storefront.heading :level="1" class="mt-3 mb-10">
            {{ __('Checkout') }}
        </x-storefront.heading>

        <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
            <form wire:submit.prevent class="space-y-8">
                <section class="bg-surface-container-lowest p-8">
                    <x-storefront.heading :level="3" class="mb-6">
                        {{ __('Contact information') }}
                    </x-storefront.heading>

                    <x-storefront.input
                        name="email"
                        type="email"
                        :label="__('Email address')"
                        autocomplete="email"
                        wire:model.live.debounce.300ms="email"
                        required
                    />
                </section>

                <section class="bg-surface-container-lowest p-8">
                    <x-storefront.heading :level="3" class="mb-6">
                        {{ __('Details') }}
                    </x-storefront.heading>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-storefront.input
                            name="firstName"
                            :label="__('First name')"
                            autocomplete="given-name"
                            wire:model.blur="firstName"
                            required
                        />

                        <x-storefront.input
                            name="lastName"
                            :label="__('Last name')"
                            autocomplete="family-name"
                            wire:model.blur="lastName"
                            required
                        />

                        <div class="sm:col-span-2">
                            <x-storefront.input
                                name="addressLine1"
                                :label="__('Address line 1')"
                                autocomplete="address-line1"
                                wire:model.blur="addressLine1"
                                required
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <x-storefront.input
                                name="addressLine2"
                                :label="__('Address line 2')"
                                autocomplete="address-line2"
                                wire:model.blur="addressLine2"
                            />
                        </div>

                        <x-storefront.input
                            name="city"
                            :label="__('City')"
                            autocomplete="address-level2"
                            wire:model.blur="city"
                            required
                        />

                        <x-storefront.select
                            name="country"
                            :label="__('Country')"
                            autocomplete="country"
                            wire:model.live="country"
                            required
                        >
                            @foreach ($countries as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </x-storefront.select>

                        <x-storefront.input
                            name="state"
                            :label="__('State / County')"
                            autocomplete="address-level1"
                            wire:model.blur="state"
                        />

                        <x-storefront.input
                            name="postalCode"
                            :label="__('Postal code')"
                            autocomplete="postal-code"
                            wire:model.live.blur="postalCode"
                            required
                        />
                    </div>
                </section>

                <section class="bg-surface-container-lowest p-8">
                    <x-storefront.heading :level="3" class="mb-6">
                        {{ __('Shipping') }}
                    </x-storefront.heading>

                    @if ($shippingQuote)
                        <div class="flex items-start justify-between gap-4 border border-outline-variant/40 bg-surface-container-low p-4">
                            <div>
                                <p class="text-body-md text-on-surface">{{ $shippingQuote->zoneName }}</p>
                                <p class="text-body-sm text-on-surface-variant">{{ $shippingQuote->methodType->label() }}</p>
                            </div>
                            <p class="text-body-md text-oak-deep">
                                @if ($shippingQuote->isFree())
                                    {{ __('Free') }}
                                @else
                                    {{ $shippingQuote->formattedCost() }}
                                @endif
                            </p>
                        </div>
                    @elseif ($shippingError)
                        <p class="text-body-sm text-error">{{ $shippingError }}</p>
                    @else
                        <p class="text-body-sm text-on-surface-variant">
                            {{ __('Enter your country and postcode to see shipping options.') }}
                        </p>
                    @endif
                </section>

                <section
                    wire:ignore.self
                    x-data="stripePaymentElement({
                        publishableKey: @js(config('payments.gateways.stripe.publishable_key')),
                        currency: @js(strtolower((string) config('payments.currency', 'GBP'))),
                    })"
                    class="bg-surface-container-lowest p-8">
                    <x-storefront.heading :level="3" class="mb-6">
                        {{ __('Payment') }}
                    </x-storefront.heading>

                    <div wire:ignore>
                        <div id="stripe-payment-element" class="min-h-[40px]"></div>
                    </div>

                    <template x-if="error">
                        <p class="mt-3 text-body-sm text-error" x-text="error"></p>
                    </template>

                    @if ($placementError)
                        <p class="mt-3 text-body-sm text-error">{{ $placementError }}</p>
                    @endif

                    <x-storefront.button
                        type="button"
                        variant="primary"
                        size="lg"
                        class="mt-6 w-full"
                        :disabled="$shippingQuote === null"
                        wire:loading.attr="disabled"
                        x-bind:disabled="! ready || processing"
                        x-on:click="onPrimaryClick()"
                    >
                        <span x-show="! processing">{{ __('Place order') }}</span>
                        <span x-show="processing" x-cloak>{{ __('Processing…') }}</span>
                    </x-storefront.button>

                    <p class="mt-3 text-body-sm text-on-surface-variant">
                        {{ __('Secure payment by Stripe. Card details are sent directly to Stripe and never touch our servers.') }}
                    </p>
                </section>
            </form>

            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="bg-surface-container-lowest">
                    <div class="border-b border-outline-variant/40 px-6 py-4">
                        <p class="text-label-sm uppercase text-on-surface-variant">{{ __('Order summary') }}</p>
                    </div>

                    <ul class="divide-y divide-outline-variant/40">
                        @foreach ($items as $item)
                            <li class="flex gap-3 p-4" wire:key="summary-{{ $item->id }}">
                                <div class="h-14 w-14 shrink-0 overflow-hidden bg-surface-container">
                                    @if ($img = $item->variant->product->primaryImage())
                                        <img src="{{ $img->getUrl() }}" alt="{{ $item->variant->product->name }}" class="h-full w-full object-cover" />
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-body-md text-on-surface">{{ $item->variant->product->name }}</p>
                                    <p class="text-body-sm text-on-surface-variant">
                                        {{ $item->variant->attributeValues->map(fn ($v) => $v->attribute->name . ': ' . $v->value)->implode(' / ') ?: $item->variant->sku }}
                                    </p>
                                    @if ($item->addons->isNotEmpty())
                                        <ul class="mt-1 text-body-sm text-on-surface-variant">
                                            @foreach ($item->addons as $addon)
                                                <li>+ {{ $addon->name }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <p class="mt-1 text-body-sm text-on-surface-variant">
                                        {{ $item->quantity }} &times; <x-storefront.price :amount="$item->unitPrice()" />
                                    </p>
                                </div>

                                <div class="text-right text-body-md text-on-surface">
                                    <x-storefront.price :amount="$item->lineTotal()" />
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="space-y-2 border-t border-outline-variant/40 bg-surface-container-low px-6 py-5">
                        <div class="flex items-center justify-between">
                            <span class="text-body-sm text-on-surface-variant">{{ __('Subtotal') }}</span>
                            <span class="text-body-sm text-on-surface">
                                <x-storefront.price :amount="$subtotal" />
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-body-sm text-on-surface-variant">{{ __('Shipping') }}</span>
                            <span class="text-body-sm text-on-surface">
                                @if ($shippingQuote)
                                    @if ($shippingQuote->isFree())
                                        {{ __('Free') }}
                                    @else
                                        <x-storefront.price :amount="$shippingCost" />
                                    @endif
                                @else
                                    <span class="text-timber-ash">&mdash;</span>
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between border-t border-outline-variant/40 pt-3">
                            <span class="text-label-sm uppercase text-on-surface-variant">{{ __('Total') }}</span>
                            <span class="text-headline-sm text-oak-deep">
                                <x-storefront.price :amount="$grandTotal" />
                            </span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('basket.show') }}" wire:navigate
                   class="mt-6 inline-block text-label-sm uppercase text-on-surface-variant hover:text-oak-deep">
                    &larr; {{ __('Return to basket') }}
                </a>
            </aside>
        </div>
    </x-storefront.section>
</div>
