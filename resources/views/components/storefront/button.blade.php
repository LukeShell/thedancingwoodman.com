@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    // Modern Rustic button language — uppercase, tracking-widest, sharp 4px corners.
    // All storefront buttons share the same base so they read as one family across
    // light and dark sections; variants only change colour + hover treatment.
    $base = 'inline-flex items-center justify-center gap-2 rounded font-sans font-bold uppercase tracking-widest transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-oak-deep disabled:opacity-50 disabled:pointer-events-none';

    $variants = [
        // Solid dark — default CTA on cream backgrounds (Etsy, Send Message, Add to basket).
        'primary' => 'bg-oak-deep text-white hover:opacity-90',

        // Outlined dark — paired with primary (Trustpilot, secondary actions).
        'secondary' => 'border border-oak-deep bg-transparent text-oak-deep hover:bg-sapwood-cream',

        // Solid cream — for use on dark backgrounds (hero CTA).
        'cream' => 'bg-sapwood-cream text-oak-deep hover:opacity-90',

        // Brand red — reserved for high-impact / accent actions.
        'accent' => 'bg-brand-accent text-white hover:opacity-90',

        // Quiet — for inline neutral actions.
        'ghost' => 'bg-transparent text-oak-deep hover:bg-surface-container-low',

        // Inline arrow link — used for "Explore Full Shop", "View all" etc.
        'link' => 'group bg-transparent text-oak-deep hover:translate-x-2 px-0 py-0',
    ];

    $sizes = [
        'sm' => 'text-label-sm px-6 py-3',
        'md' => 'text-label-sm px-8 py-4',
        'lg' => 'text-label-md px-10 py-5',
    ];

    // Link variant ignores the padded sizes — it should read as inline text.
    if ($variant === 'link') {
        $sizes = [
            'sm' => 'text-label-sm',
            'md' => 'text-label-md',
            'lg' => 'text-label-md',
        ];
    }

    $classes = trim(
        $base.' '
        .($variants[$variant] ?? $variants['primary']).' '
        .($sizes[$size] ?? $sizes['md'])
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
