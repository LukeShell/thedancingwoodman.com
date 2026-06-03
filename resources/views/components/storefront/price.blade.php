@props([
    'amount' => 0,
    'from' => false,
    'currency' => '£',
])

@php
    $formatted = $currency . number_format((float) $amount, 2);
@endphp

<span {{ $attributes }}>
    @if ($from)
        <span class="text-label-sm uppercase text-on-surface-variant">{{ __('From') }}</span>
    @endif
    <span>{{ $formatted }}</span>
</span>
