@extends('layouts.storefront')

@php
    $categories = [
        [
            'label' => __('Dining Tables'),
            'meta' => __('Large Rectangular | Round | Indoor & Outdoor'),
            'span' => 'md:col-span-8',
            'image' => asset('images/home-bento-table.jpg'),
        ],
        [
            'label' => __('Coffee Tables'),
            'meta' => __('Wooden Coffee | Side Tables'),
            'span' => 'md:col-span-4',
            'image' => asset('images/home-bento-coffee.jpg'),
        ],
        [
            'label' => __('Doors'),
            'meta' => __('Industrial Steel Runner | Farmhouse'),
            'span' => 'md:col-span-5',
            'image' => asset('images/home-bento-door.jpg'),
        ],
        [
            'label' => __('Accessories'),
            'meta' => __('Bath Caddies | Serving Trays | Boards'),
            'span' => 'md:col-span-7',
            'image' => asset('images/home-bento-bath.jpg'),
        ],
    ];
@endphp

@section('content')
    {{-- Hero --}}
    <section class="relative flex h-[85vh] items-center overflow-hidden bg-oak-deep" style="background-image: url('{{ asset('images/wooden-rustic-furniture.png') }}'); background-size: cover; background-position: center;">
        <!-- <div class="bg-grain absolute inset-0 z-0 bg-primary-container opacity-90"></div> -->
        <!-- <div class="absolute inset-0 z-0 bg-gradient-to-r from-oak-deep via-oak-deep/70 to-transparent"></div> -->

        <x-storefront.container as="div" class="relative z-10 text-white">
            <div class="max-w-2xl border-l-4 border-sapwood-cream bg-oak-deep/40 p-12 backdrop-blur-sm">
                <p class="mb-4 font-sans text-label-md uppercase tracking-[0.2em] text-sapwood-cream/90">
                    {{ __('Furniture for inside and out') }}
                </p>
                <h1 class="mb-8 font-display text-3xl leading-tight text-white sm:text-headline-xl">
                    {{ __('The Outside Is On') }}
                </h1>
                <x-storefront.button :href="route('shop.index')" variant="cream" size="lg">
                    {{ __('Shop Our Outdoor Range') }}
                </x-storefront.button>
            </div>
        </x-storefront.container>
    </section>

    {{-- Category bento grid --}}
    <section class="py-24">
        <x-storefront.container as="div">
            <div class="grid h-auto grid-cols-1 gap-gutter md:h-[900px] md:grid-cols-12">
                @foreach ($categories as $category)
                    <a href="{{ route('shop.index') }}"
                       class="group relative h-[400px] overflow-hidden {{ $category['span'] }} md:h-full">
                        <img src="{{ $category['image'] }}"
                             alt="{{ $category['label'] }}"
                             loading="lazy"
                             class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-gradient-to-t from-oak-deep/90 via-transparent to-transparent group-hover:opacity-70 transition-opacity opacity-100"></div>
                        <div class="absolute bottom-10 left-10 text-white">
                            <h3 class="mb-2 font-display text-headline-lg text-white">{{ $category['label'] }}</h3>
                            <p class="font-sans text-label-md uppercase tracking-wider text-sapwood-cream/80">
                                {{ $category['meta'] }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </x-storefront.container>
    </section>

    {{-- Best sellers --}}
    <section class="relative bg-surface-container-low py-24">
        <div class="bg-grain pointer-events-none absolute inset-0"></div>
        <x-storefront.container as="div" class="relative">
            <div class="mb-16 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <x-storefront.heading :level="2">{{ __('Our Best Sellers') }}</x-storefront.heading>
                    <div class="mt-4 h-1 w-20 bg-oak-deep"></div>
                </div>
                <x-storefront.button :href="route('shop.index')" variant="link" size="md">
                    {{ __('Explore Full Shop') }}
                    <span aria-hidden="true">&rarr;</span>
                </x-storefront.button>
            </div>

            @if ($featured->isEmpty())
                <x-storefront.empty-state :message="__('Featured pieces will appear here soon.')" />
            @else
                <div class="grid grid-cols-1 gap-gutter sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featured as $product)
                        <x-storefront.product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </x-storefront.container>
    </section>

    {{-- A bit about us --}}
    <section class="py-32">
        <x-storefront.container as="div">
            <div class="grid grid-cols-1 items-center gap-24 lg:grid-cols-2">
                <div class="relative">
                    <div class="bg-grain aspect-[4/5] overflow-hidden bg-primary-container"></div>
                    <div class="absolute -bottom-12 -right-12 hidden aspect-square w-2/3 border-[16px] border-sapwood-cream shadow-2xl md:block">
                        <div class="bg-grain h-full w-full bg-secondary-fixed-dim"></div>
                    </div>
                </div>

                <div class="space-y-8">
                    <x-storefront.heading :level="2" size="xl">{{ __('A bit about us') }}</x-storefront.heading>
                    <p class="max-w-lg font-sans text-body-lg leading-relaxed text-on-surface-variant">
                        {{ __('Our furniture is full of individual rustic charm, pieces that are timeless and bursting with character. Each piece is handmade to order with love and care by our dedicated team in Southampton.') }}
                    </p>
                    <p class="max-w-lg font-sans text-body-md text-on-surface-variant">
                        {{ __("Sustainability is at the heart of what we do. From locally sourced reclaimed timber and eco-friendly finishes to 100% recycled packaging, we ensure every Dancing Woodman piece respects the material's history while building a future for your home.") }}
                    </p>
                    <div class="pt-6">
                        <a href="#" class="group inline-flex items-center gap-4 text-oak-deep">
                            <span class="flex h-12 w-12 items-center justify-center rounded-full border border-oak-deep transition-all group-hover:bg-oak-deep group-hover:text-white">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </span>
                            <span class="font-sans text-label-md font-bold uppercase tracking-widest">{{ __('Our Workshop Story') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </x-storefront.container>
    </section>

    {{-- As featured in --}}
    <section class="border-y border-timber-ash/20 bg-white py-20">
        <x-storefront.container as="div">
            <p class="mb-12 text-center font-sans text-label-sm uppercase tracking-widest text-timber-ash">
                {{ __('As Featured In') }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-12 text-timber-ash md:gap-24">
                <span class="font-display text-2xl font-bold uppercase tracking-tight">BBC</span>
                <span class="font-display text-2xl font-bold uppercase tracking-tight">Forbes</span>
                <span class="font-display text-3xl tracking-tight text-oak-deep">Reclaim</span>
            </div>
        </x-storefront.container>
    </section>

    {{-- Testimonials --}}
    <section class="py-32 text-center">
        <x-storefront.container as="div">
            <div class="mx-auto max-w-3xl">
                <div class="mb-8 flex justify-center gap-1 text-[#00b67a]">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    @endfor
                </div>
                <x-storefront.heading :level="2" class="mb-6">
                    {{ __('See What Our Customers Say…') }}
                </x-storefront.heading>
                <p class="mb-12 font-sans text-body-lg leading-relaxed text-on-surface-variant">
                    {{ __('“Our handmade furniture has brought joy to thousands of customers who value thoughtful craftsmanship and lasting quality. The 5-star reviews we’ve earned on Etsy and Trustpilot reflect the experience we strive to provide every time.”') }}
                </p>
                <div class="flex flex-wrap justify-center gap-6">
                    <x-storefront.button href="#" variant="primary" size="md">
                        {{ __('Etsy Reviews') }}
                    </x-storefront.button>
                    <x-storefront.button href="#" variant="secondary" size="md">
                        {{ __('Trustpilot Reviews') }}
                    </x-storefront.button>
                </div>
            </div>
        </x-storefront.container>
    </section>

    {{-- Contact CTA --}}
    <section class="relative overflow-hidden bg-oak-deep py-32 text-sapwood-cream">
        <div class="bg-grain pointer-events-none absolute inset-0 opacity-30"></div>
        <x-storefront.container as="div" class="relative z-10">
            <div class="grid grid-cols-1 items-center gap-gutter lg:grid-cols-2">
                <div>
                    <h2 class="mb-6 font-display text-3xl leading-tight text-sapwood-cream sm:text-headline-xl">
                        {{ __('Need Furniture For Your Business?') }}
                    </h2>
                    <p class="mb-10 max-w-md font-sans text-body-lg opacity-80">
                        {{ __("From boutique cafes to modern office spaces, we create bespoke solutions that reflect your brand's commitment to quality and craftsmanship.") }}
                    </p>
                    <div class="space-y-4 font-sans text-body-md">
                        <p class="flex items-center gap-4">
                            <svg class="h-5 w-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16v12H4z M4 6l8 7 8-7" />
                            </svg>
                            <span>hello@thedancingwoodman.com</span>
                        </p>
                        <p class="flex items-center gap-4">
                            <svg class="h-5 w-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M3 5a2 2 0 012-2h2l2 5-2.5 1.5a11 11 0 005 5L17 12l5 2v2a2 2 0 01-2 2h-1A16 16 0 013 6V5z" />
                            </svg>
                            <span>01489 795283</span>
                        </p>
                    </div>
                </div>

                <div class="bg-sapwood-cream p-10 text-oak-deep">
                    <form class="space-y-6" method="POST" action="#">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <input type="text" name="name" placeholder="{{ __('Name') }}"
                                   class="w-full border-x-0 border-t-0 border-b border-timber-ash bg-transparent px-0 py-3 font-sans text-body-md focus:border-oak-deep focus:ring-0" />
                            <input type="email" name="email" placeholder="{{ __('Email Address') }}"
                                   class="w-full border-x-0 border-t-0 border-b border-timber-ash bg-transparent px-0 py-3 font-sans text-body-md focus:border-oak-deep focus:ring-0" />
                        </div>
                        <input type="tel" name="phone" placeholder="{{ __('Telephone Number') }}"
                               class="w-full border-x-0 border-t-0 border-b border-timber-ash bg-transparent px-0 py-3 font-sans text-body-md focus:border-oak-deep focus:ring-0" />
                        <textarea name="message" rows="4" placeholder="{{ __('Message *') }}"
                                  class="w-full border-x-0 border-t-0 border-b border-timber-ash bg-transparent px-0 py-3 font-sans text-body-md focus:border-oak-deep focus:ring-0"></textarea>
                        <x-storefront.button type="submit" variant="primary" size="lg" class="w-full">
                            {{ __('Send Message') }}
                        </x-storefront.button>
                    </form>
                </div>
            </div>
        </x-storefront.container>
    </section>
@endsection
