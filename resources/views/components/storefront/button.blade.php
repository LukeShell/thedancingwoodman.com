@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded font-sans font-medium tracking-wide transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-oak-deep disabled:opacity-50 disabled:pointer-events-none';

    $variants = [
        'primary' => 'bg-oak-deep text-on-primary hover:bg-primary-fixed-variant',
        'secondary' => 'border border-timber-ash bg-transparent text-oak-deep hover:bg-surface-container-low',
        'ghost' => 'bg-transparent text-oak-deep hover:bg-surface-container-low',
        'link' => 'bg-transparent text-oak-deep underline-offset-4 hover:underline px-0 py-0',
        'accent' => 'bg-brand-accent text-white hover:opacity-90',
    ];

    $sizes = [
        'sm' => 'text-label-md px-4 py-2',
        'md' => 'text-label-md px-6 py-3',
        'lg' => 'text-body-md px-8 py-4',
    ];

    if ($variant === 'link') {
        $sizes['sm'] = 'text-label-md';
        $sizes['md'] = 'text-label-md';
        $sizes['lg'] = 'text-body-md';
    }

    $classes = trim(
        $base . ' '
        . ($variants[$variant] ?? $variants['primary']) . ' '
        . ($sizes[$size] ?? $sizes['md'])
    );
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" @disabled($disabled) {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
