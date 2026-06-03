@extends('layouts.storefront')

@section('content')
    <nav class="mb-6 text-sm text-stone-500 dark:text-stone-400">
        <a href="{{ route('home') }}" class="hover:text-amber-700">Home</a>
        <span class="mx-1">/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-amber-700">Shop</a>
        <span class="mx-1">/</span>
        <span class="text-stone-900 dark:text-stone-100">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-stone-200 bg-stone-100 dark:border-stone-800 dark:bg-stone-800">
            <div class="aspect-square">
                @if ($img = $product->primaryImage())
                    <img src="{{ $img->getUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                @else
                    <div class="flex h-full w-full items-center justify-center text-stone-400">
                        <svg class="h-24 w-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>
        </div>

        <div>
            @if ($product->categories->isNotEmpty())
                <div class="mb-3 flex flex-wrap gap-2">
                    @foreach ($product->categories as $category)
                        <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                           class="rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-700 hover:bg-stone-200 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <h1 class="mb-3 text-3xl font-bold tracking-tight">{{ $product->name }}</h1>

            <p class="mb-6 text-2xl font-semibold text-amber-700 dark:text-amber-500">
                From &pound;{{ number_format($product->base_price, 2) }}
            </p>

            @foreach ($product->attributes as $attribute)
                <div class="mb-5">
                    <h3 class="mb-2 text-sm font-semibold uppercase tracking-wider text-stone-500">{{ $attribute->name }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($attribute->values as $value)
                            <span class="rounded-md border border-stone-300 px-3 py-1.5 text-sm dark:border-stone-700">
                                {{ $value->value }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <livewire:storefront.add-to-basket-form :product="$product" />
        </div>
    </div>

    @if ($product->description)
        <div class="mb-8 text-stone-700 dark:text-stone-300">
            <p>{!! $product->description !!}</p>
        </div>
    @endif

    @if ($product->variants->isNotEmpty())
        <section class="mt-12">
            <h2 class="mb-4 text-xl font-semibold tracking-tight">Available variants</h2>
            <div class="overflow-x-auto rounded-lg border border-stone-200 dark:border-stone-800">
                <table class="w-full text-sm">
                    <thead class="bg-stone-50 text-left text-xs uppercase tracking-wider text-stone-500 dark:bg-stone-900">
                        <tr>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Options</th>
                            <th class="px-4 py-3">Stock</th>
                            <th class="px-4 py-3 text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 dark:divide-stone-800">
                        @foreach ($product->variants as $variant)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">{{ $variant->sku }}</td>
                                <td class="px-4 py-3">
                                    {{ $variant->attributeValues->map(fn ($v) => $v->attribute->name . ': ' . $v->value)->implode(' / ') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($variant->stock_quantity > 0)
                                        <span class="text-emerald-700 dark:text-emerald-400">{{ $variant->stock_quantity }} in stock</span>
                                    @else
                                        <span class="text-stone-500">Made to order</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium">&pound;{{ number_format($variant->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
@endsection
