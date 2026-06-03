@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'required' => false,
])

@php
    $selectId = $id ?? $name;
    $hasError = $name && $errors->has($name);
    $borderClass = $hasError ? 'border-b-error' : 'border-b-timber-ash focus:border-b-oak-deep';
@endphp

<div class="flex flex-col gap-2">
    @if ($label)
        <label for="{{ $selectId }}" class="text-label-md uppercase text-on-surface-variant">
            {{ $label }}
            @if ($required)
                <span class="text-brand-accent" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    <select
        @if ($name) name="{{ $name }}" @endif
        id="{{ $selectId }}"
        @if ($required) required @endif
        {{ $attributes->class('w-full appearance-none bg-sapwood-cream border-0 border-b ' . $borderClass . ' bg-[url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'8\' viewBox=\'0 0 12 8\' fill=\'none\'><path d=\'M1 1l5 5 5-5\' stroke=\'%23251D15\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/></svg>")] bg-[length:12px_8px] bg-[right_0.25rem_center] bg-no-repeat px-1 py-3 pr-8 text-body-md text-charcoal-text focus:outline-none focus:ring-0 transition-colors') }}
    >
        {{ $slot }}
    </select>

    @if ($name)
        @error($name)
            <p class="text-body-sm text-error">{{ $message }}</p>
        @enderror
    @endif
</div>
