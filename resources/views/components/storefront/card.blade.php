@props([
    'as' => 'div',
    'padding' => 'md',
    'hover' => false,
])

@php
    $pad = match ($padding) {
        'none' => '',
        'sm' => 'p-4',
        'lg' => 'p-8',
        default => 'p-6',
    };

    $hoverClass = $hover ? 'transition hover:shadow-rustic-hover' : '';
@endphp

<{{ $as }} {{ $attributes->class(trim('bg-surface-container-lowest text-on-surface ' . $pad . ' ' . $hoverClass)) }}>
    {{ $slot }}
</{{ $as }}>
