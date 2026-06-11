<div>
    <x-storefront.section size="sm">
        <div class="grid grid-cols-1 items-start gap-12 lg:grid-cols-12 lg:gap-16">
            {{-- Left column: checkout form --}}
            <form wire:submit.prevent class="space-y-12 lg:col-span-7">
                {{-- Step 1 — Contact --}}
                <section id="contact-section" class="space-y-6">
                    <div class="flex items-center justify-between">
                        <x-storefront.heading :level="2" size="md">{{ __('Contact Information') }}</x-storefront.heading>
                        <span class="text-label-sm uppercase tracking-widest text-timber-ash">{{ __('Step 1 of 3') }}</span>
                    </div>

                    <x-storefront.input
                        name="email"
                        type="email"
                        :label="__('Email address')"
                        placeholder="email@example.com"
                        autocomplete="email"
                        variant="white"
                        wire:model.live.debounce.300ms="email"
                        required
                    />
                </section>

                <hr class="border-timber-ash/20" />

                {{-- Step 2 — Shipping address --}}
                <section id="shipping-section" class="space-y-6">
                    <div class="flex items-center justify-between">
                        <x-storefront.heading :level="2" size="md">{{ __('Shipping Address') }}</x-storefront.heading>
                        <span class="text-label-sm uppercase tracking-widest text-timber-ash">{{ __('Step 2 of 3') }}</span>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <x-storefront.input
                            name="firstName"
                            :label="__('First name')"
                            autocomplete="given-name"
                            variant="white"
                            wire:model.blur="firstName"
                            required
                        />

                        <x-storefront.input
                            name="lastName"
                            :label="__('Last name')"
                            autocomplete="family-name"
                            variant="white"
                            wire:model.blur="lastName"
                            required
                        />

                        <div class="md:col-span-2">
                            <x-storefront.input
                                name="addressLine1"
                                :label="__('Address')"
                                placeholder="123 Artisan Way"
                                autocomplete="address-line1"
                                variant="white"
                                wire:model.blur="addressLine1"
                                required
                            />
                        </div>

                        <div class="md:col-span-2">
                            <x-storefront.input
                                name="addressLine2"
                                :label="__('Apartment, suite, etc. (optional)')"
                                autocomplete="address-line2"
                                variant="white"
                                wire:model.blur="addressLine2"
                            />
                        </div>

                        <x-storefront.input
                            name="city"
                            :label="__('City')"
                            autocomplete="address-level2"
                            variant="white"
                            wire:model.blur="city"
                            required
                        />

                        <x-storefront.input
                            name="postalCode"
                            :label="__('Postal Code')"
                            autocomplete="postal-code"
                            variant="white"
                            wire:model.live.blur="postalCode"
                            required
                        />

                        <x-storefront.select
                            name="country"
                            :label="__('Country')"
                            autocomplete="country"
                            variant="white"
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
                            variant="white"
                            wire:model.blur="state"
                        />
                    </div>

                    {{-- Address-driven shipping resolution feedback --}}
                    <div class="pt-2">
                        @if ($shippingQuote)
                            <div class="flex items-start justify-between gap-4 border border-timber-ash/30 bg-sapwood-cream p-4">
                                <div>
                                    <p class="text-label-md uppercase tracking-wider text-oak-deep">
                                        {{ $shippingQuote->zoneName }}
                                    </p>
                                    <p class="text-body-md text-secondary">
                                        {{ $shippingQuote->methodType->label() }}
                                    </p>
                                </div>
                                <p class="text-label-md uppercase tracking-wider text-oak-deep">
                                    @if ($shippingQuote->isFree())
                                        {{ __('Free') }}
                                    @else
                                        {{ $shippingQuote->formattedCost() }}
                                    @endif
                                </p>
                            </div>
                        @elseif ($shippingError)
                            <p class="text-body-md text-error">{{ $shippingError }}</p>
                        @else
                            <p class="text-body-md text-secondary">
                                {{ __('Enter your country and postcode to see your delivery cost.') }}
                            </p>
                        @endif
                    </div>
                </section>

                <hr class="border-timber-ash/20" />

                {{-- Step 3 — Payment --}}
                <section
                    id="payment-section"
                    class="space-y-6"
                    wire:ignore.self
                    x-data="stripePaymentElement({
                        publishableKey: @js(config('payments.gateways.stripe.publishable_key')),
                        currency: @js(strtolower((string) config('payments.currency', 'GBP'))),
                    })"
                >
                    <div class="flex items-center justify-between">
                        <x-storefront.heading :level="2" size="md">{{ __('Payment') }}</x-storefront.heading>
                        <span class="text-label-sm uppercase tracking-widest text-timber-ash">{{ __('Step 3 of 3') }}</span>
                    </div>

                    <div class="space-y-6 border border-timber-ash/20 bg-sapwood-cream p-6">
                        {{-- Gateway selector — scaffolded so additional providers slot in
                             as sibling labels without restructuring. --}}
                        <div class="space-y-3" role="radiogroup" aria-label="{{ __('Payment method') }}">
                            <label class="flex cursor-pointer items-center gap-4 border border-timber-ash/30 bg-white p-4 transition-colors hover:border-oak-deep">
                                <input
                                    type="radio"
                                    name="paymentGateway"
                                    value="stripe"
                                    wire:model="selectedGateway"
                                    class="text-oak-deep focus:ring-0"
                                />
                                <div class="flex-grow">
                                    <p class="text-label-md uppercase tracking-wider text-oak-deep">{{ __('Pay by card') }}</p>
                                    <p class="text-body-md text-secondary">{{ __('Secure card payment via Stripe.') }}</p>
                                </div>
                                <span class="material-symbols-outlined text-timber-ash" aria-hidden="true">credit_card</span>
                            </label>
                        </div>

                        <div wire:ignore>
                            <div id="stripe-payment-element" class="min-h-[40px]"></div>
                        </div>

                        <template x-if="error">
                            <p class="text-body-md text-error" x-text="error"></p>
                        </template>

                        @if ($placementError)
                            <p class="text-body-md text-error">{{ $placementError }}</p>
                        @endif
                    </div>

                    <x-storefront.button
                        type="button"
                        variant="primary"
                        size="lg"
                        class="w-full"
                        :disabled="$shippingQuote === null"
                        wire:loading.attr="disabled"
                        x-bind:disabled="! ready || processing"
                        x-on:click="onPrimaryClick()"
                    >
                        <span x-show="! processing">
                            {{ __('Complete Purchase') }} &mdash; <x-storefront.price :amount="$grandTotal" />
                        </span>
                        <span x-show="processing" x-cloak>{{ __('Processing…') }}</span>
                    </x-storefront.button>

                    <p class="flex items-center justify-center gap-2 text-label-sm uppercase tracking-widest text-timber-ash">
                        <span class="material-symbols-outlined text-[14px]" aria-hidden="true">lock</span>
                        {{ __('All transactions are secure and encrypted.') }}
                    </p>
                </section>
            </form>

            {{-- Right column: sticky order summary --}}
            <aside class="lg:col-span-5 lg:sticky lg:top-32 lg:self-start">
                <div class="space-y-8 bg-sapwood-cream p-6 md:p-8">
                    <h3 class="border-b border-timber-ash/20 pb-4 font-display text-headline-md text-oak-deep">
                        {{ __('Order Summary') }}
                    </h3>

                    {{-- Items --}}
                    <div class="space-y-6">
                        @foreach ($items as $item)
                            @php
                                $product = $item->variant->product;
                                $image = $product->primaryImage();
                                $imageUrl = $image
                                    ? ($image->hasGeneratedConversion('card') ? $image->getUrl('card') : $image->getUrl())
                                    : null;
                                $variantLine = $item->variant->attributeValues
                                    ->map(fn ($v) => $v->value)
                                    ->implode(' / ');
                            @endphp

                            <div class="flex gap-4" wire:key="summary-{{ $item->id }}">
                                <div class="h-24 w-20 shrink-0 overflow-hidden bg-surface-container">
                                    @if ($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                                    @endif
                                </div>

                                <div class="flex flex-grow flex-col justify-between py-1">
                                    <div>
                                        <p class="text-label-md uppercase tracking-wider text-oak-deep">{{ $product->name }}</p>
                                        @if ($variantLine !== '')
                                            <p class="text-label-sm uppercase tracking-wider text-timber-ash">{{ $variantLine }}</p>
                                        @endif
                                        @if ($item->finish)
                                            <p class="text-label-sm uppercase tracking-wider text-timber-ash">
                                                {{ $item->finish->name }}
                                            </p>
                                        @endif
                                        @if ($item->addons->isNotEmpty())
                                            <ul class="mt-1 text-label-sm uppercase tracking-wider text-timber-ash">
                                                @foreach ($item->addons as $addon)
                                                    <li>+ {{ $addon->name }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <p class="mt-1 text-body-md text-secondary">{{ __('Qty') }} {{ $item->quantity }}</p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <x-storefront.price :amount="$item->lineTotal()" class="text-label-md uppercase tracking-wider text-oak-deep" />
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Discount code --}}
                    <div>
                        @if ($discount)
                            <div class="flex items-center justify-between gap-2 border-b border-timber-ash/30 pb-2">
                                <div>
                                    <p class="text-label-sm uppercase tracking-widest text-timber-ash">{{ __('Discount Code') }}</p>
                                    <p class="text-body-md font-medium text-oak-deep">{{ $discount->code }}</p>
                                </div>
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
                                    type="text"
                                    wire:model="promoCode"
                                    wire:keydown.enter.prevent="applyPromo"
                                    placeholder="{{ __('Discount code') }}"
                                    class="flex-grow border-0 border-b border-timber-ash/30 bg-sapwood-cream px-1 py-3 text-body-md text-charcoal-text placeholder:text-timber-ash focus:border-b-oak-deep focus:outline-none focus:ring-0"
                                />
                                <button
                                    type="button"
                                    wire:click="applyPromo"
                                    class="bg-timber-ash/20 px-6 text-label-sm uppercase tracking-widest text-oak-deep transition-colors hover:bg-timber-ash/30"
                                >
                                    {{ __('Apply') }}
                                </button>
                            </div>
                        @endif
                        @if ($promoError)
                            <p class="mt-2 text-label-sm text-error">{{ $promoError }}</p>
                        @endif
                        @if ($promoSuccess)
                            <p class="mt-2 text-label-sm text-secondary">{{ $promoSuccess }}</p>
                        @endif
                    </div>

                    {{-- Totals --}}
                    <div class="space-y-3 border-t border-timber-ash/20 pt-4">
                        <div class="flex justify-between">
                            <span class="text-body-md text-secondary">{{ __('Subtotal') }}</span>
                            <x-storefront.price :amount="$subtotal" class="text-label-md uppercase tracking-wider text-oak-deep" />
                        </div>

                        @if ($discount)
                            <div class="flex justify-between">
                                <span class="text-body-md text-secondary">
                                    {{ __('Discount') }}
                                    <span class="ml-1 text-label-sm uppercase tracking-wider text-timber-ash">({{ $discount->code }})</span>
                                </span>
                                <span class="text-label-md uppercase tracking-wider text-brand-accent">
                                    -<x-storefront.price :amount="$discountAmount" />
                                </span>
                            </div>
                        @endif

                        <div class="flex justify-between">
                            <span class="text-body-md text-secondary">{{ __('Shipping') }}</span>
                            <span class="text-label-md uppercase tracking-wider text-oak-deep">
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

                        <div class="flex items-end justify-between pt-4">
                            <span class="text-label-md uppercase tracking-widest text-oak-deep">{{ __('Total') }}</span>
                            <div class="text-right">
                                <span class="mr-2 text-label-sm uppercase tracking-wider text-timber-ash">
                                    {{ strtoupper(config('payments.currency', 'GBP')) }}
                                </span>
                                <x-storefront.price :amount="$grandTotal" class="font-display text-headline-md text-oak-deep" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Trust badges --}}
                <div class="mt-8 grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 border border-timber-ash/20 p-4">
                        <span class="material-symbols-outlined text-timber-ash" aria-hidden="true">inventory_2</span>
                        <p class="text-label-sm uppercase leading-tight tracking-wider text-secondary">{{ __('Eco-Friendly Packaging') }}</p>
                    </div>
                    <div class="flex items-center gap-3 border border-timber-ash/20 p-4">
                        <span class="material-symbols-outlined text-timber-ash" aria-hidden="true">local_shipping</span>
                        <p class="text-label-sm uppercase leading-tight tracking-wider text-secondary">{{ __('Hand-Checked Quality') }}</p>
                    </div>
                </div>

                <a href="{{ route('basket.show') }}" wire:navigate
                   class="mt-6 inline-block text-label-sm uppercase tracking-widest text-timber-ash transition-colors hover:text-oak-deep">
                    &larr; {{ __('Return to basket') }}
                </a>
            </aside>
        </div>
    </x-storefront.section>
</div>
