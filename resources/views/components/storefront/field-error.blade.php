@props(['name'])

@error($name)
    <p {{ $attributes->class('text-body-sm text-error') }}>{{ $message }}</p>
@enderror
