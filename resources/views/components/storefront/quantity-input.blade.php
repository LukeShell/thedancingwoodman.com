@props([
    'name' => null,
    'id' => null,
    'value' => 1,
    'min' => 1,
    'max' => 99,
    'label' => null,
])

@php
    $inputId = $id ?? ('qty-' . ($name ?? uniqid()));
@endphp

<div class="inline-flex items-center gap-2">
    @if ($label)
        <label for="{{ $inputId }}" class="sr-only">{{ $label }}</label>
    @endif
    <input
        type="number"
        @if ($name) name="{{ $name }}" @endif
        id="{{ $inputId }}"
        value="{{ $value }}"
        min="{{ $min }}"
        max="{{ $max }}"
        {{ $attributes->class('w-20 bg-sapwood-cream border-0 border-b border-b-timber-ash focus:border-b-oak-deep px-2 py-2 text-center text-body-md text-charcoal-text focus:outline-none focus:ring-0 transition-colors') }}
    />
</div>
