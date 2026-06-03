@props(['as' => 'div'])

<{{ $as }} {{ $attributes->class('mx-auto w-full max-w-content px-4 sm:px-8 lg:px-12') }}>
    {{ $slot }}
</{{ $as }}>
