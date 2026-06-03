@extends('layouts.storefront')

@section('content')
    <x-storefront.section>
        <div class="mb-12">
            <x-storefront.eyebrow>{{ __('Shop') }}</x-storefront.eyebrow>
            <x-storefront.heading :level="1" class="mt-3">
                @if ($activeCategory)
                    {{ $activeCategory->name }}
                @else
                    {{ __('Every piece, handmade.') }}
                @endif
            </x-storefront.heading>
            @if ($activeCategory)
                <p class="mt-4 text-body-md text-on-surface-variant">
                    {{ __('Showing :category', ['category' => $activeCategory->name]) }} &middot;
                    <a href="{{ route('shop.index') }}" class="text-oak-deep underline-offset-4 hover:underline">
                        {{ __('Clear filter') }}
                    </a>
                </p>
            @else
                <p class="mt-4 text-body-lg text-on-surface-variant">
                    {{ __('Every piece is handmade in our workshop, using sustainable and reclaimed timber.') }}
                </p>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-[220px_1fr]">
            <aside>
                <p class="mb-4 text-label-sm uppercase text-on-surface-variant">
                    {{ __('Categories') }}
                </p>
                <ul class="space-y-1 text-body-md">
                    <li>
                        <a href="{{ route('shop.index') }}"
                           class="block px-3 py-2 transition {{ ! $activeCategory ? 'bg-oak-deep text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                            {{ __('All products') }}
                        </a>
                    </li>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                               class="block px-3 py-2 transition {{ $activeCategory?->id === $category->id ? 'bg-oak-deep text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}">
                                {{ $category->name }}
                            </a>
                            @if ($category->children->isNotEmpty())
                                <ul class="ml-3 mt-1 space-y-1 border-l border-outline-variant/40 pl-3">
                                    @foreach ($category->children as $child)
                                        <li>
                                            <a href="{{ route('shop.index', ['category' => $child->slug]) }}"
                                               class="block px-3 py-1.5 text-body-sm transition {{ $activeCategory?->id === $child->id ? 'text-oak-deep' : 'text-on-surface-variant hover:text-oak-deep' }}">
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
                    <x-storefront.empty-state :message="__('No products found in this category yet.')" />
                @else
                    <div class="grid grid-cols-1 gap-x-8 gap-y-12 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($products as $product)
                            <x-storefront.product-card :product="$product" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </x-storefront.section>
@endsection
