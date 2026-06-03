<div>
    <h1 class="mb-6 text-3xl font-bold tracking-tight">{{ __('Checkout') }}</h1>

    <div class="grid gap-8 lg:grid-cols-[1fr_380px]">
        <form wire:submit.prevent class="space-y-6">
            <section class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-800 dark:bg-stone-900">
                <h2 class="mb-4 text-lg font-semibold">{{ __('Contact information') }}</h2>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium">{{ __('Email address') }} <span class="text-red-600">*</span></label>
                    <input id="email" type="email" autocomplete="email" wire:model.live.debounce.300ms="email"
                           class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600 dark:border-stone-700 dark:bg-stone-900 @error('email') border-red-500 @enderror" />
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </section>

            <section class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-800 dark:bg-stone-900">
                <h2 class="mb-4 text-lg font-semibold">{{ __('Details') }}</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="firstName" class="mb-1 block text-sm font-medium">{{ __('First name') }} <span class="text-red-600">*</span></label>
                        <input id="firstName" type="text" autocomplete="given-name" wire:model.blur="firstName"
                               class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600 dark:border-stone-700 dark:bg-stone-900 @error('firstName') border-red-500 @enderror" />
                        @error('firstName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="lastName" class="mb-1 block text-sm font-medium">{{ __('Last name') }} <span class="text-red-600">*</span></label>
                        <input id="lastName" type="text" autocomplete="family-name" wire:model.blur="lastName"
                               class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600 dark:border-stone-700 dark:bg-stone-900 @error('lastName') border-red-500 @enderror" />
                        @error('lastName') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="addressLine1" class="mb-1 block text-sm font-medium">{{ __('Address line 1') }} <span class="text-red-600">*</span></label>
                        <input id="addressLine1" type="text" autocomplete="address-line1" wire:model.blur="addressLine1"
                               class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600 dark:border-stone-700 dark:bg-stone-900 @error('addressLine1') border-red-500 @enderror" />
                        @error('addressLine1') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="addressLine2" class="mb-1 block text-sm font-medium">{{ __('Address line 2') }}</label>
                        <input id="addressLine2" type="text" autocomplete="address-line2" wire:model.blur="addressLine2"
                               class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600 dark:border-stone-700 dark:bg-stone-900 @error('addressLine2') border-red-500 @enderror" />
                        @error('addressLine2') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="city" class="mb-1 block text-sm font-medium">{{ __('City') }} <span class="text-red-600">*</span></label>
                        <input id="city" type="text" autocomplete="address-level2" wire:model.blur="city"
                               class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600 dark:border-stone-700 dark:bg-stone-900 @error('city') border-red-500 @enderror" />
                        @error('city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="country" class="mb-1 block text-sm font-medium">{{ __('Country') }} <span class="text-red-600">*</span></label>
                        <select id="country" autocomplete="country" wire:model.live="country"
                                class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600 dark:border-stone-700 dark:bg-stone-900 @error('country') border-red-500 @enderror">
                            @foreach ($countries as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('country') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="state" class="mb-1 block text-sm font-medium">{{ __('State / County') }}</label>
                        <input id="state" type="text" autocomplete="address-level1" wire:model.blur="state"
                               class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600 dark:border-stone-700 dark:bg-stone-900 @error('state') border-red-500 @enderror" />
                        @error('state') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="postalCode" class="mb-1 block text-sm font-medium">{{ __('Postal code') }} <span class="text-red-600">*</span></label>
                        <input id="postalCode" type="text" autocomplete="postal-code" wire:model.live.blur="postalCode"
                               class="w-full rounded-md border border-stone-300 px-3 py-2 text-sm focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600 dark:border-stone-700 dark:bg-stone-900 @error('postalCode') border-red-500 @enderror" />
                        @error('postalCode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-800 dark:bg-stone-900">
                <h2 class="mb-4 text-lg font-semibold">{{ __('Shipping') }}</h2>

                @if ($shippingQuote)
                    <div class="flex items-start justify-between gap-4 rounded-md border border-stone-200 bg-stone-50 p-4 dark:border-stone-800 dark:bg-stone-900/60">
                        <div>
                            <p class="text-sm font-medium">{{ $shippingQuote->zoneName }}</p>
                            <p class="text-xs text-stone-500 dark:text-stone-400">{{ $shippingQuote->methodType->label() }}</p>
                        </div>
                        <p class="text-sm font-semibold">
                            @if ($shippingQuote->isFree())
                                {{ __('Free') }}
                            @else
                                {{ $shippingQuote->formattedCost() }}
                            @endif
                        </p>
                    </div>
                @elseif ($shippingError)
                    <p class="text-sm text-red-600">{{ $shippingError }}</p>
                @else
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Enter your country and postcode to see shipping options.') }}</p>
                @endif
            </section>

            <section
                wire:ignore.self
                x-data="stripePaymentElement({
                    publishableKey: @js(config('payments.gateways.stripe.publishable_key')),
                    currency: @js(strtolower((string) config('payments.currency', 'GBP'))),
                })"
                class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-800 dark:bg-stone-900">
                <h2 class="mb-4 text-lg font-semibold">{{ __('Payment') }}</h2>

                <div wire:ignore>
                    <div id="stripe-payment-element" class="min-h-[40px]"></div>
                </div>

                <template x-if="error">
                    <p class="mt-3 text-sm text-red-600" x-text="error"></p>
                </template>

                @if ($placementError)
                    <p class="mt-3 text-sm text-red-600">{{ $placementError }}</p>
                @endif

                <button
                    type="button"
                    wire:loading.attr="disabled"
                    @disabled($shippingQuote === null)
                    x-bind:disabled="! ready || processing"
                    x-on:click="onPrimaryClick()"
                    class="mt-4 w-full rounded-md bg-amber-700 px-5 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-offset-stone-900">
                    <span x-show="! processing">{{ __('Place order') }}</span>
                    <span x-show="processing" x-cloak>{{ __('Processing…') }}</span>
                </button>

                <p class="mt-2 text-xs text-stone-500 dark:text-stone-400">
                    {{ __('Secure payment by Stripe. Card details are sent directly to Stripe and never touch our servers.') }}
                </p>
            </section>
        </form>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="overflow-hidden rounded-xl border border-stone-200 bg-white dark:border-stone-800 dark:bg-stone-900">
                <div class="border-b border-stone-200 px-4 py-3 dark:border-stone-800">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500">{{ __('Order summary') }}</h2>
                </div>

                <ul class="divide-y divide-stone-200 dark:divide-stone-800">
                    @foreach ($items as $item)
                        <li class="flex gap-3 p-4" wire:key="summary-{{ $item->id }}">
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-md border border-stone-200 bg-stone-100 dark:border-stone-800 dark:bg-stone-800">
                                @if ($img = $item->variant->product->primaryImage())
                                    <img src="{{ $img->getUrl() }}" alt="{{ $item->variant->product->name }}" class="h-full w-full object-cover" />
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ $item->variant->product->name }}</p>
                                <p class="text-xs text-stone-500 dark:text-stone-400">
                                    {{ $item->variant->attributeValues->map(fn ($v) => $v->attribute->name . ': ' . $v->value)->implode(' / ') ?: $item->variant->sku }}
                                </p>
                                @if ($item->addons->isNotEmpty())
                                    <ul class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                                        @foreach ($item->addons as $addon)
                                            <li>+ {{ $addon->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                                    {{ $item->quantity }} &times; &pound;{{ number_format((float) $item->unitPrice(), 2) }}
                                </p>
                            </div>

                            <div class="text-right text-sm font-medium">
                                &pound;{{ number_format((float) $item->lineTotal(), 2) }}
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="space-y-2 border-t border-stone-200 bg-stone-50 px-4 py-4 dark:border-stone-800 dark:bg-stone-900/60">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-stone-600 dark:text-stone-400">{{ __('Subtotal') }}</span>
                        <span class="text-sm">&pound;{{ number_format($subtotal, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-stone-600 dark:text-stone-400">{{ __('Shipping') }}</span>
                        <span class="text-sm">
                            @if ($shippingQuote)
                                @if ($shippingQuote->isFree())
                                    {{ __('Free') }}
                                @else
                                    &pound;{{ number_format($shippingCost, 2) }}
                                @endif
                            @else
                                <span class="text-stone-400">&mdash;</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-t border-stone-200 pt-2 dark:border-stone-800">
                        <span class="text-sm uppercase tracking-wider text-stone-500">{{ __('Total') }}</span>
                        <span class="text-lg font-semibold">&pound;{{ number_format($grandTotal, 2) }}</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('basket.show') }}" wire:navigate
               class="mt-4 inline-block text-sm text-stone-500 hover:text-amber-700">
                &larr; {{ __('Return to basket') }}
            </a>
        </aside>
    </div>
</div>
