@props([
    'size' => 'md',
    'as' => 'p',
])

@php
    $typeClass = match ($size) {
        'lg' => 'text-body-lg',
        'sm' => 'text-body-sm',
        default => 'text-body-md',
    };
@endphp

<{{ $as }} {{ $attributes->class('font-sans text-on-surface ' . $typeClass) }}>
    {{ $slot }}
</{{ $as }}>
