<div @if (! $this->isTerminal()) wire:poll.2s.visible="refreshStatus" @endif>
    <div class="mx-auto max-w-2xl">
        @if ($paid)
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-8 text-center dark:border-emerald-900/60 dark:bg-emerald-950/40">
                <h1 class="mb-2 text-2xl font-bold tracking-tight text-emerald-900 dark:text-emerald-100">
                    {{ __('Thank you for your order!') }}
                </h1>
                <p class="text-sm text-emerald-800 dark:text-emerald-200">
                    {{ __('Order') }} <span class="font-semibold">{{ $order->reference }}</span> {{ __('has been confirmed. A receipt has been emailed to') }} {{ $order->email }}.
                </p>
            </div>
        @elseif ($failed)
            <div class="rounded-xl border border-red-200 bg-red-50 p-8 text-center dark:border-red-900/60 dark:bg-red-950/40">
                <h1 class="mb-2 text-2xl font-bold tracking-tight text-red-900 dark:text-red-100">
                    {{ __('Payment failed') }}
                </h1>
                <p class="text-sm text-red-800 dark:text-red-200">
                    {{ __('Your payment could not be processed. Please try again.') }}
                </p>
                <a href="{{ route('checkout.show') }}" wire:navigate
                   class="mt-4 inline-block rounded-md bg-amber-700 px-5 py-2 text-sm font-medium text-white hover:bg-amber-800">
                    {{ __('Return to checkout') }}
                </a>
            </div>
        @elseif ($cancelled)
            <div class="rounded-xl border border-stone-200 bg-stone-50 p-8 text-center dark:border-stone-800 dark:bg-stone-900">
                <h1 class="mb-2 text-2xl font-bold tracking-tight">{{ __('Order cancelled') }}</h1>
                <p class="text-sm text-stone-600 dark:text-stone-400">
                    {{ __('This order was cancelled. Nothing has been charged.') }}
                </p>
            </div>
        @else
            <div class="rounded-xl border border-stone-200 bg-white p-8 text-center dark:border-stone-800 dark:bg-stone-900">
                <h1 class="mb-2 text-2xl font-bold tracking-tight">{{ __('Confirming your payment…') }}</h1>
                <p class="text-sm text-stone-600 dark:text-stone-400">
                    {{ __('Order') }} <span class="font-semibold">{{ $order->reference }}</span> &mdash; {{ __('we are waiting for payment confirmation. This usually takes a few seconds.') }}
                </p>
                <div class="mt-6 inline-flex h-6 w-6 animate-spin rounded-full border-2 border-amber-600 border-t-transparent"></div>
            </div>
        @endif

        <div class="mt-8 rounded-xl border border-stone-200 bg-white dark:border-stone-800 dark:bg-stone-900">
            <div class="border-b border-stone-200 px-4 py-3 dark:border-stone-800">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-500">{{ __('Order summary') }}</h2>
            </div>

            <ul class="divide-y divide-stone-200 dark:divide-stone-800">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-3 p-4">
                        <div>
                            <p class="text-sm font-medium">{{ $item->product_name }}</p>
                            @if ($item->variant_label)
                                <p class="text-xs text-stone-500 dark:text-stone-400">{{ $item->variant_label }}</p>
                            @endif
                            @if ($item->addons->isNotEmpty())
                                <ul class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                                    @foreach ($item->addons as $addon)
                                        <li>+ {{ $addon->name }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <p class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                                {{ $item->quantity }} &times; &pound;{{ number_format($item->unit_price / 100, 2) }}
                            </p>
                        </div>
                        <div class="text-right text-sm font-medium">
                            &pound;{{ number_format($item->line_total / 100, 2) }}
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="flex items-center justify-between border-t border-stone-200 bg-stone-50 px-4 py-4 dark:border-stone-800 dark:bg-stone-900/60">
                <span class="text-sm uppercase tracking-wider text-stone-500">{{ __('Total') }}</span>
                <span class="text-lg font-semibold">&pound;{{ number_format($order->grand_total / 100, 2) }}</span>
            </div>
        </div>
    </div>
</div>
