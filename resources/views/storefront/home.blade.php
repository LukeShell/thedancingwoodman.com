@extends('layouts.storefront')

@section('content')
    <section class="rounded-2xl bg-gradient-to-br from-amber-50 to-stone-100 p-10 dark:from-stone-800 dark:to-stone-900 lg:p-16">
        <div class="max-w-2xl">
            <p class="mb-3 text-sm font-medium uppercase tracking-widest text-amber-700 dark:text-amber-500">Handmade in Britain</p>
            <h1 class="mb-4 text-4xl font-bold tracking-tight lg:text-5xl">
                Rustic wooden furniture, built to last a lifetime.
            </h1>
            <p class="mb-6 text-lg text-stone-600 dark:text-stone-300">
                Each piece is hand-crafted in our workshop using reclaimed and sustainable timber. Tables, units, and benches made to your specification.
            </p>
            <a href="{{ route('shop.index') }}"
               class="inline-flex items-center rounded-md bg-stone-900 px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-stone-800 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-white">
                Shop the collection
            </a>
        </div>
    </section>

    <section class="mt-12">
        <div class="mb-6 flex items-baseline justify-between">
            <h2 class="text-2xl font-semibold tracking-tight">Featured pieces</h2>
            <a href="{{ route('shop.index') }}" class="text-sm text-amber-700 hover:underline dark:text-amber-500">View all &rarr;</a>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured as $product)
                <a href="{{ route('shop.show', $product) }}"
                   class="group block overflow-hidden rounded-xl border border-stone-200 bg-white transition hover:shadow-lg dark:border-stone-800 dark:bg-stone-900">
                    <div class="aspect-square bg-stone-100 dark:bg-stone-800">
                        @if ($img = $product->primaryImage())
                            <img src="{{ $img->getUrl('card') }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                        @else
                            <div class="flex h-full w-full items-center justify-center text-stone-400">
                                <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="mb-1 font-medium group-hover:text-amber-700 dark:group-hover:text-amber-500">{{ $product->name }}</h3>
                        <p class="text-sm text-stone-500 dark:text-stone-400">From &pound;{{ number_format($product->base_price, 2) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
