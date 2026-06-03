@props([
    'size' => 'md',
    'inset' => false,
    'as' => 'section',
])

@php
    $padding = match ($size) {
        'sm' => 'py-12 lg:py-16',
        'lg' => 'py-24 lg:py-32',
        default => 'py-20 lg:py-24',
    };

    $background = $inset ? 'bg-surface-container-low' : '';
@endphp

<{{ $as }} {{ $attributes->class(trim($padding . ' ' . $background)) }}>
    <x-storefront.container>
        {{ $slot }}
    </x-storefront.container>
</{{ $as }}>
