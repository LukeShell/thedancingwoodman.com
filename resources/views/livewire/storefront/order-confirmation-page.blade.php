<div @if (! $this->isTerminal()) wire:poll.2s.visible="refreshStatus" @endif>
    <x-storefront.section>
        <div class="mx-auto max-w-2xl">
            @if ($paid)
                <div class="border border-primary-fixed bg-primary-fixed p-10 text-center">
                    <x-storefront.eyebrow class="!text-on-primary-fixed-variant">
                        {{ __('Order confirmed') }}
                    </x-storefront.eyebrow>
                    <x-storefront.heading :level="1" class="mt-3">
                        {{ __('Thank you for your order!') }}
                    </x-storefront.heading>
                    <p class="mt-4 text-body-md text-on-primary-fixed-variant">
                        {{ __('Order') }}
                        <span class="font-semibold text-oak-deep">{{ $order->reference }}</span>
                        {{ __('has been confirmed. A receipt has been emailed to') }}
                        <span class="text-oak-deep">{{ $order->email }}</span>.
                    </p>
                </div>
            @elseif ($failed)
                <div class="border border-error-container bg-error-container p-10 text-center">
                    <x-storefront.heading :level="1" class="!text-on-error-container">
                        {{ __('Payment failed') }}
                    </x-storefront.heading>
                    <p class="mt-4 text-body-md text-on-error-container">
                        {{ __('Your payment could not be processed. Please try again.') }}
                    </p>
                    <x-storefront.button :href="route('checkout.show')" variant="primary" class="mt-6" wire:navigate>
                        {{ __('Return to checkout') }}
                    </x-storefront.button>
                </div>
            @elseif ($cancelled)
                <div class="border border-outline-variant bg-surface-container-low p-10 text-center">
                    <x-storefront.heading :level="1">{{ __('Order cancelled') }}</x-storefront.heading>
                    <p class="mt-4 text-body-md text-on-surface-variant">
                        {{ __('This order was cancelled. Nothing has been charged.') }}
                    </p>
                </div>
            @else
                <div class="bg-surface-container-lowest p-10 text-center">
                    <x-storefront.heading :level="1">
                        {{ __('Confirming your payment…') }}
                    </x-storefront.heading>
                    <p class="mt-4 text-body-md text-on-surface-variant">
                        {{ __('Order') }}
                        <span class="font-semibold text-oak-deep">{{ $order->reference }}</span>
                        &mdash; {{ __('we are waiting for payment confirmation. This usually takes a few seconds.') }}
                    </p>
                    <div class="mt-8 inline-flex h-6 w-6 animate-spin rounded-full border-2 border-oak-deep border-t-transparent"></div>
                </div>
            @endif

            <div class="mt-10 bg-surface-container-lowest">
                <div class="border-b border-outline-variant/40 px-6 py-4">
                    <p class="text-label-sm uppercase text-on-surface-variant">{{ __('Order summary') }}</p>
                </div>

                <ul class="divide-y divide-outline-variant/40">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between gap-3 p-4">
                            <div>
                                <p class="text-body-md text-on-surface">{{ $item->product_name }}</p>
                                @if ($item->variant_label)
                                    <p class="text-body-sm text-on-surface-variant">{{ $item->variant_label }}</p>
                                @endif
                                @if ($item->addons->isNotEmpty())
                                    <ul class="mt-1 text-body-sm text-on-surface-variant">
                                        @foreach ($item->addons as $addon)
                                            <li>+ {{ $addon->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <p class="mt-1 text-body-sm text-on-surface-variant">
                                    {{ $item->quantity }} &times; <x-storefront.price :amount="$item->unit_price / 100" />
                                </p>
                            </div>
                            <div class="text-right text-body-md text-on-surface">
                                <x-storefront.price :amount="$item->line_total / 100" />
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="flex items-center justify-between border-t border-outline-variant/40 bg-surface-container-low px-6 py-5">
                    <span class="text-label-sm uppercase text-on-surface-variant">{{ __('Total') }}</span>
                    <span class="text-headline-sm text-oak-deep">
                        <x-storefront.price :amount="$order->grand_total / 100" />
                    </span>
                </div>
            </div>
        </div>
    </x-storefront.section>
</div>
