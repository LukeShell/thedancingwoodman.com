@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'autocomplete' => null,
    'hint' => null,
    'variant' => 'cream',
])

@php
    $inputId = $id ?? $name;
    $describedBy = [];
    if ($hint) {
        $describedBy[] = $inputId . '-hint';
    }
    if ($name && $errors->has($name)) {
        $describedBy[] = $inputId . '-error';
    }

    $borderClass = ($name && $errors->has($name))
        ? 'border-b-error'
        : 'border-b-timber-ash focus:border-b-oak-deep';

    $backgroundClass = $variant === 'white' ? 'bg-white' : 'bg-sapwood-cream';
@endphp

<div {{ $attributes->only(['class'])->class('flex flex-col gap-2') }}>
    @if ($label)
        <label for="{{ $inputId }}" class="text-label-md uppercase text-on-surface-variant">
            {{ $label }}
            @if ($required)
                <span class="text-brand-accent" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        @if ($name) name="{{ $name }}" @endif
        id="{{ $inputId }}"
        @if (! is_null($value)) value="{{ $value }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if ($required) required @endif
        @if (count($describedBy)) aria-describedby="{{ implode(' ', $describedBy) }}" @endif
        {{ $attributes->except(['class'])->class('w-full ' . $backgroundClass . ' border-0 border-b ' . $borderClass . ' px-1 py-3 text-body-md text-charcoal-text placeholder:text-timber-ash focus:outline-none focus:ring-0 transition-colors') }}
    />

    @if ($hint)
        <p id="{{ $inputId }}-hint" class="text-body-sm text-on-surface-variant">{{ $hint }}</p>
    @endif

    @if ($name)
        @error($name)
            <p id="{{ $inputId }}-error" class="text-body-sm text-error">{{ $message }}</p>
        @enderror
    @endif
</div>
