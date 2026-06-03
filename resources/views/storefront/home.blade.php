@extends('layouts.storefront')

@section('content')
    <x-storefront.grain-backdrop>
        <x-storefront.section size="lg" class="!py-24 lg:!py-32">
            <div class="max-w-2xl">
                <x-storefront.eyebrow>{{ __('Handmade in Britain') }}</x-storefront.eyebrow>
                <x-storefront.heading :level="1" class="mt-4">
                    {{ __('Rustic wooden furniture, built to last a lifetime.') }}
                </x-storefront.heading>
                <x-storefront.prose size="lg" class="mt-6 text-on-surface-variant">
                    {{ __('Each piece is hand-crafted in our workshop using reclaimed and sustainable timber. Tables, units, and benches made to your specification.') }}
                </x-storefront.prose>
                <x-storefront.button :href="route('shop.index')" variant="primary" size="lg" class="mt-8">
                    {{ __('Shop the collection') }}
                </x-storefront.button>
            </div>
        </x-storefront.section>
    </x-storefront.grain-backdrop>

    <x-storefront.section>
        <div class="mb-12 flex items-end justify-between">
            <div>
                <x-storefront.eyebrow>{{ __('The Collection') }}</x-storefront.eyebrow>
                <x-storefront.heading :level="2" class="mt-3">
                    {{ __('Featured pieces') }}
                </x-storefront.heading>
            </div>
            <x-storefront.button :href="route('shop.index')" variant="link" size="md">
                {{ __('View all') }} &rarr;
            </x-storefront.button>
        </div>

        @if ($featured->isEmpty())
            <x-storefront.empty-state :message="__('Featured pieces will appear here soon.')" />
        @else
            <div class="grid grid-cols-1 gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $product)
                    <x-storefront.product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </x-storefront.section>
@endsection
