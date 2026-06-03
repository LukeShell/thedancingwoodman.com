<div>
    <h1 class="mb-6 text-3xl font-bold tracking-tight">{{ __('Your basket') }}</h1>

    @if ($items->isEmpty())
        <div class="rounded-xl border border-dashed border-stone-300 bg-white p-12 text-center dark:border-stone-700 dark:bg-stone-900">
            <p class="mb-4 text-stone-500 dark:text-stone-400">{{ __('Your basket is empty.') }}</p>
            <a href="{{ route('shop.index') }}"
               class="inline-block rounded-md bg-stone-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-stone-800 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-stone-200">
                {{ __('Browse the shop') }}
            </a>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-stone-200 bg-white dark:border-stone-800 dark:bg-stone-900">
            <ul class="divide-y divide-stone-200 dark:divide-stone-800">
                @foreach ($items as $item)
                    <li class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start" wire:key="item-{{ $item->id }}">
                        <div class="h-20 w-20 shrink-0 overflow-hidden rounded-md border border-stone-200 bg-stone-100 dark:border-stone-800 dark:bg-stone-800">
                            @if ($img = $item->variant->product->primaryImage())
                                <img src="{{ $img->getUrl() }}" alt="{{ $item->variant->product->name }}" class="h-full w-full object-cover" />
                            @endif
                        </div>

                        <div class="flex-1">
                            <a href="{{ route('shop.show', $item->variant->product) }}"
                               class="font-semibold hover:text-amber-700">
                                {{ $item->variant->product->name }}
                            </a>
                            <p class="text-sm text-stone-500 dark:text-stone-400">
                                {{ $item->variant->attributeValues->map(fn ($v) => $v->attribute->name . ': ' . $v->value)->implode(' / ') ?: $item->variant->sku }}
                            </p>
                            @if ($item->addons->isNotEmpty())
                                <ul class="mt-1 text-xs text-stone-500 dark:text-stone-400">
                                    @foreach ($item->addons as $addon)
                                        <li>+ {{ $addon->name }} (&pound;{{ number_format($addon->price, 2) }})</li>
                                    @endforeach
                                </ul>
                            @endif
                            <p class="mt-2 text-sm font-medium">&pound;{{ number_format((float) $item->unitPrice(), 2) }} {{ __('each') }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="sr-only" for="qty-{{ $item->id }}">{{ __('Quantity') }}</label>
                            <input id="qty-{{ $item->id }}" type="number" min="1" max="99"
                                   value="{{ $item->quantity }}"
                                   wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                   class="w-20 rounded-md border-stone-300 px-3 py-2 text-sm dark:border-stone-700 dark:bg-stone-900" />
                            <button type="button" wire:click="removeItem({{ $item->id }})"
                                    class="text-sm text-stone-500 hover:text-red-600">
                                {{ __('Remove') }}
                            </button>
                        </div>

                        <div class="text-right sm:w-28">
                            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Line total') }}</p>
                            <p class="font-semibold">&pound;{{ number_format((float) $item->lineTotal(), 2) }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="flex flex-col gap-4 border-t border-stone-200 bg-stone-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-stone-800 dark:bg-stone-900/60">
                <div class="flex items-center justify-between gap-4 sm:justify-start">
                    <span class="text-sm uppercase tracking-wider text-stone-500">{{ __('Subtotal') }}</span>
                    <span class="text-xl font-semibold">&pound;{{ number_format($subtotal, 2) }}</span>
                </div>

                <a href="{{ route('checkout.show') }}" wire:navigate
                   class="inline-block rounded-md bg-amber-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-amber-700">
                    {{ __('Proceed to checkout') }}
                </a>
            </div>
        </div>
    @endif
</div>
