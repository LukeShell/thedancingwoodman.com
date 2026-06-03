@extends('layouts.storefront')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">Shop</h1>
        @if ($activeCategory)
            <p class="mt-2 text-stone-600 dark:text-stone-400">
                Showing <span class="font-medium">{{ $activeCategory->name }}</span>
                &middot;
                <a href="{{ route('shop.index') }}" class="text-amber-700 hover:underline dark:text-amber-500">Clear filter</a>
            </p>
        @else
            <p class="mt-2 text-stone-600 dark:text-stone-400">Every piece is handmade in our workshop.</p>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-[220px_1fr]">
        <aside>
            <h2 class="mb-3 text-sm font-semibold uppercase tracking-wider text-stone-500">Categories</h2>
            <ul class="space-y-1 text-sm">
                <li>
                    <a href="{{ route('shop.index') }}"
                       class="block rounded px-2 py-1.5 hover:bg-stone-100 dark:hover:bg-stone-800 {{ ! $activeCategory ? 'bg-stone-900 text-white dark:bg-stone-100 dark:text-stone-900' : '' }}">
                        All products
                    </a>
                </li>
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                           class="block rounded px-2 py-1.5 hover:bg-stone-100 dark:hover:bg-stone-800 {{ $activeCategory?->id === $category->id ? 'bg-stone-900 text-white dark:bg-stone-100 dark:text-stone-900' : '' }}">
                            {{ $category->name }}
                        </a>
                        @if ($category->children->isNotEmpty())
                            <ul class="ml-3 mt-1 space-y-1 border-l border-stone-200 pl-3 dark:border-stone-700">
                                @foreach ($category->children as $child)
                                    <li>
                                        <a href="{{ route('shop.index', ['category' => $child->slug]) }}"
                                           class="block rounded px-2 py-1 text-stone-600 hover:bg-stone-100 dark:text-stone-400 dark:hover:bg-stone-800 {{ $activeCategory?->id === $child->id ? 'font-medium text-stone-900 dark:text-stone-100' : '' }}">
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </aside>

        <div>
            @if ($products->isEmpty())
                <p class="rounded-lg border border-dashed border-stone-300 p-8 text-center text-stone-500 dark:border-stone-700 dark:text-stone-400">
                    No products found in this category yet.
                </p>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($products as $product)
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
                                @if ($product->short_description)
                                    <p class="mb-2 line-clamp-2 text-sm text-stone-500 dark:text-stone-400">{{ $product->short_description }}</p>
                                @endif
                                <p class="text-sm font-medium">From &pound;{{ number_format($product->base_price, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
