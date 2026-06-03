@props([
    'title' => null,
    'message' => null,
])

<div {{ $attributes->class('flex flex-col items-center justify-center gap-4 border border-dashed border-outline-variant bg-surface-container-lowest p-12 text-center') }}>
    @if ($title)
        <h3 class="font-display text-headline-md text-oak-deep">{{ $title }}</h3>
    @endif

    @if ($message)
        <p class="text-body-md text-on-surface-variant">{{ $message }}</p>
    @endif

    {{ $slot }}
</div>
