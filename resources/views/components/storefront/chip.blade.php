@props(['tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-surface-container-high text-on-surface-variant',
        'accent' => 'bg-brand-accent text-white',
        'success' => 'bg-primary-fixed text-on-primary-fixed-variant',
        'inverse' => 'bg-oak-deep text-on-primary',
    ];

    $classes = 'inline-flex items-center rounded-full px-3 py-1 text-label-sm font-sans uppercase ' . ($tones[$tone] ?? $tones['neutral']);
@endphp

<span {{ $attributes->class($classes) }}>
    {{ $slot }}
</span>
