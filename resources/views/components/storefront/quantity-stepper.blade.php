@props([
    'value' => 1,
    'min' => 1,
    'max' => 99,
    'decrement' => null,
    'increment' => null,
    'label' => null,
    'id' => null,
])

@php
    $stepperId = $id ?? 'qty-'.uniqid();
@endphp

<div {{ $attributes->class('inline-flex items-center border border-timber-ash/30') }}>
    @if ($label)
        <span id="{{ $stepperId }}-label" class="sr-only">{{ $label }}</span>
    @endif
    <button
        type="button"
        @if ($decrement) wire:click="{{ $decrement }}" @endif
        aria-label="{{ __('Decrease quantity') }}"
        class="px-3 text-oak-deep transition-colors hover:bg-surface-container"
    >&minus;</button>
    <span
        @if ($label) aria-labelledby="{{ $stepperId }}-label" @endif
        aria-live="polite"
        class="w-8 text-center font-medium text-oak-deep"
    >{{ $value }}</span>
    <button
        type="button"
        @if ($increment) wire:click="{{ $increment }}" @endif
        aria-label="{{ __('Increase quantity') }}"
        class="px-3 text-oak-deep transition-colors hover:bg-surface-container"
    >+</button>
</div>
