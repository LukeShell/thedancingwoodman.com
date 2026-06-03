@extends('layouts.storefront')

@section('content')
    <x-storefront.section>
        <x-storefront.breadcrumb
            :items="[
                ['label' => __('Home'), 'url' => route('home')],
                ['label' => __('Shop'), 'url' => route('shop.index')],
                ['label' => $product->name],
            ]"
            class="mb-10"
        />

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            <div class="bg-surface-container">
                <div class="aspect-square">
                    @if ($img = $product->primaryImage())
                        <img src="{{ $img->getUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                    @else
                        <div class="flex h-full w-full items-center justify-center text-timber-ash">
                            <x-storefront.placeholder-image class="h-24 w-24" />
                        </div>
                    @endif
                </div>
            </div>

            <div>
                @if ($product->categories->isNotEmpty())
                    <div class="mb-6 flex flex-wrap gap-2">
                        @foreach ($product->categories as $category)
                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}">
                                <x-storefront.chip tone="neutral">
                                    {{ $category->name }}
                                </x-storefront.chip>
                            </a>
                        @endforeach
                    </div>
                @endif

                <x-storefront.heading :level="1">{{ $product->name }}</x-storefront.heading>

                <p class="mt-6 text-headline-md text-oak-deep">
                    <x-storefront.price :amount="$product->base_price" from />
                </p>

                @foreach ($product->attributes as $attribute)
                    <div class="mt-8">
                        <p class="mb-3 text-label-sm uppercase text-on-surface-variant">
                            {{ $attribute->name }}
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($attribute->values as $value)
                                <x-storefront.chip tone="neutral">
                                    {{ $value->value }}
                                </x-storefront.chip>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="mt-10">
                    <livewire:storefront.add-to-basket-form :product="$product" />
                </div>
            </div>
        </div>

        @if ($product->description)
            <div class="mt-16 max-w-3xl">
                <x-storefront.divider class="mb-10" />
                <div class="prose prose-lg max-w-none text-on-surface font-sans text-body-md leading-relaxed">
                    {!! $product->description !!}
                </div>
            </div>
        @endif

        @if ($product->variants->isNotEmpty())
            <div class="mt-16">
                <x-storefront.heading :level="2" class="mb-6">
                    {{ __('Available variants') }}
                </x-storefront.heading>
                <div class="overflow-x-auto border border-outline-variant/40">
                    <table class="w-full text-body-sm">
                        <thead class="bg-surface-container-low text-left text-label-sm uppercase text-on-surface-variant">
                            <tr>
                                <th class="px-4 py-3 font-medium">{{ __('SKU') }}</th>
                                <th class="px-4 py-3 font-medium">{{ __('Options') }}</th>
                                <th class="px-4 py-3 font-medium">{{ __('Stock') }}</th>
                                <th class="px-4 py-3 text-right font-medium">{{ __('Price') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/40">
                            @foreach ($product->variants as $variant)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-body-sm">{{ $variant->sku }}</td>
                                    <td class="px-4 py-3">
                                        {{ $variant->attributeValues->map(fn ($v) => $v->attribute->name . ': ' . $v->value)->implode(' / ') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($variant->stock_quantity > 0)
                                            <x-storefront.chip tone="success">
                                                {{ __(':n in stock', ['n' => $variant->stock_quantity]) }}
                                            </x-storefront.chip>
                                        @else
                                            <span class="text-on-surface-variant">{{ __('Made to order') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <x-storefront.price :amount="$variant->price" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </x-storefront.section>
@endsection
