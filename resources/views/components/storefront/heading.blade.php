@props([
    'level' => 2,
    'size' => null,
])

@php
    $level = (int) $level;
    $tag = 'h' . max(1, min(6, $level));

    $size ??= match ($level) {
        1 => 'xl',
        2 => 'lg',
        3 => 'md',
        default => 'sm',
    };

    $typeClass = match ($size) {
        'xl' => 'text-3xl sm:text-headline-xl',
        'lg' => 'text-headline-lg',
        'md' => 'text-headline-md',
        default => 'text-headline-sm',
    };
@endphp

<{{ $tag }} {{ $attributes->class('font-display text-oak-deep ' . $typeClass) }}>
    {{ $slot }}
</{{ $tag }}>
