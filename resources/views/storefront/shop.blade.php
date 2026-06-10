@extends('layouts.storefront')

@section('content')
    <x-storefront.container as="div" class="py-12">
        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb"
             class="mb-8 flex items-center gap-2 text-label-sm uppercase tracking-widest text-timber-ash">
            <a href="{{ route('home') }}" class="hover:text-oak-deep">{{ __('Home') }}</a>
            <span aria-hidden="true">/</span>
            <span class="font-bold text-oak-deep">
                {{ $activeCategory?->name ?? __('Shop All') }}
            </span>
        </nav>

        <div class="flex flex-col gap-12 md:flex-row">
            {{-- Sidebar --}}
            <aside class="w-full flex-shrink-0 space-y-10 md:w-64">
                <section>
                    <h3 class="mb-6 text-label-sm uppercase tracking-[0.2em] text-oak-deep">
                        {{ __('Collections') }}
                    </h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="{{ route('shop.index') }}"
                               class="flex items-center justify-between text-body-md transition-colors {{ ! $activeCategory ? 'font-bold text-oak-deep' : 'text-secondary hover:text-oak-deep' }}">
                                <span>{{ __('Shop All') }}</span>
                                <span class="text-label-sm text-timber-ash">{{ $totalCount }}</span>
                            </a>
                        </li>
                        @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                                   class="flex items-center justify-between text-body-md transition-colors {{ $activeCategory?->id === $category->id ? 'font-bold text-oak-deep' : 'text-secondary hover:text-oak-deep' }}">
                                    <span>{{ $category->name }}</span>
                                    <span class="text-label-sm text-timber-ash">{{ $category->products_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <section class="border border-timber-ash/10 bg-sapwood-cream p-6">
                    <h4 class="mb-2 font-display text-headline-md text-oak-deep">{{ __('Custom Build?') }}</h4>
                    <p class="mb-4 text-body-md text-secondary">
                        {{ __('We take bespoke commissions for any space.') }}
                    </p>
                    <a href="#"
                       class="inline-block border-b border-oak-deep pb-1 text-label-sm uppercase tracking-widest text-oak-deep transition-opacity hover:opacity-70">
                        {{ __('Learn More') }}
                    </a>
                </section>
            </aside>

            {{-- Product Grid --}}
            <section class="flex-1">
                {{-- Toolbar --}}
                <div class="mb-10 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <h1 class="font-display text-headline-lg text-oak-deep">
                        {{ $activeCategory?->name ?? __('All Products') }}
                    </h1>
                    <div class="flex items-center gap-6">
                        @if ($products->total() > 0)
                            <span class="text-label-sm uppercase tracking-widest text-timber-ash">
                                {{ __('Showing :from–:to of :total', [
                                    'from' => $products->firstItem(),
                                    'to' => $products->lastItem(),
                                    'total' => $products->total(),
                                ]) }}
                            </span>
                        @endif

                        <form method="GET" action="{{ route('shop.index') }}" class="relative">
                            @if ($activeCategory)
                                <input type="hidden" name="category" value="{{ $activeCategory->slug }}">
                            @endif
                            <select name="sort"
                                    onchange="this.form.submit()"
                                    class="cursor-pointer appearance-none border-x-0 border-t-0 border-b border-timber-ash bg-transparent py-1 pl-0 pr-8 text-label-md focus:border-oak-deep focus:ring-0">
                                <option value="featured" @selected($sort === 'featured')>{{ __('Sort By: Featured') }}</option>
                                <option value="price_asc" @selected($sort === 'price_asc')>{{ __('Price: Low to High') }}</option>
                                <option value="price_desc" @selected($sort === 'price_desc')>{{ __('Price: High to Low') }}</option>
                                <option value="newest" @selected($sort === 'newest')>{{ __('Newest Arrivals') }}</option>
                            </select>
                            <span aria-hidden="true"
                                  class="pointer-events-none absolute bottom-1 right-0 text-timber-ash">▾</span>
                        </form>
                    </div>
                </div>

                {{-- Grid --}}
                @if ($products->isEmpty())
                    <x-storefront.empty-state :message="__('No products found in this category yet.')" />
                @else
                    <div class="grid grid-cols-1 gap-x-gutter gap-y-12 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($products as $product)
                            @php
                                $image = $product->primaryImage();
                                $primaryCategory = $product->categories->first();
                            @endphp
                            <a href="{{ route('shop.show', $product) }}" class="group block cursor-pointer">
                                <div class="relative mb-4 aspect-square overflow-hidden bg-surface-container">
                                    @if ($image)
                                        <img src="{{ $image->getUrl('card') }}"
                                             alt="{{ $product->name }}"
                                             loading="lazy"
                                             class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-timber-ash">
                                            <x-storefront.placeholder-image />
                                        </div>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-sans text-body-lg text-oak-deep transition-colors group-hover:text-secondary">
                                        {{ $product->name }}
                                    </h3>
                                    @if ($primaryCategory)
                                        <p class="text-label-md uppercase tracking-wider text-timber-ash">
                                            {{ $primaryCategory->name }}
                                        </p>
                                    @endif
                                    <p class="mt-2 text-body-md font-bold text-oak-deep">
                                        <x-storefront.price :amount="$product->base_price" from />
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($products->hasPages())
                        <nav aria-label="Pagination" class="mt-20 flex items-center justify-center gap-4">
                            @if ($products->onFirstPage())
                                <span class="flex h-10 w-10 items-center justify-center border border-timber-ash/30 text-timber-ash">‹</span>
                            @else
                                <a href="{{ $products->previousPageUrl() }}"
                                   rel="prev"
                                   class="flex h-10 w-10 items-center justify-center border border-timber-ash/30 text-oak-deep transition-all hover:bg-oak-deep hover:text-white">‹</a>
                            @endif

                            @foreach (range(1, $products->lastPage()) as $page)
                                @if ($page === $products->currentPage())
                                    <span class="flex h-10 w-10 items-center justify-center bg-oak-deep font-bold text-white">{{ $page }}</span>
                                @else
                                    <a href="{{ $products->url($page) }}"
                                       class="flex h-10 w-10 items-center justify-center border border-timber-ash/30 text-oak-deep transition-all hover:bg-oak-deep hover:text-white">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($products->hasMorePages())
                                <a href="{{ $products->nextPageUrl() }}"
                                   rel="next"
                                   class="flex h-10 w-10 items-center justify-center border border-timber-ash/30 text-oak-deep transition-all hover:bg-oak-deep hover:text-white">›</a>
                            @else
                                <span class="flex h-10 w-10 items-center justify-center border border-timber-ash/30 text-timber-ash">›</span>
                            @endif
                        </nav>
                    @endif
                @endif
            </section>
        </div>
    </x-storefront.container>
@endsection
