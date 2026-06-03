@props([
    'product',
    'showDescription' => true,
])

@php
    $image = $product->primaryImage();
@endphp

<a href="{{ route('shop.show', $product) }}"
   {{ $attributes->class('group block bg-surface-container-lowest transition hover:shadow-rustic-hover') }}>
    <div class="aspect-square overflow-hidden bg-surface-container">
        @if ($image)
            <img src="{{ $image->getUrl('card') }}"
                 alt="{{ $product->name }}"
                 loading="lazy"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]" />
        @else
            <div class="flex h-full w-full items-center justify-center text-timber-ash">
                <x-storefront.placeholder-image />
            </div>
        @endif
    </div>

    <div class="px-1 pt-5 pb-2">
        <h3 class="font-display text-headline-sm text-oak-deep">
            {{ $product->name }}
        </h3>

        @if ($showDescription && $product->short_description)
            <p class="mt-2 line-clamp-2 text-body-sm text-on-surface-variant">
                {{ $product->short_description }}
            </p>
        @endif

        <p class="mt-3 text-label-md uppercase text-oak-deep">
            <x-storefront.price :amount="$product->base_price" from />
        </p>
    </div>
</a>
