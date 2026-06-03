@props(['as' => 'div'])

<{{ $as }} {{ $attributes->class('bg-grain bg-surface-container-low') }}>
    {{ $slot }}
</{{ $as }}>
