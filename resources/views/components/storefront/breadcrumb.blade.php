@props(['items' => []])

@if (count($items))
    <nav aria-label="Breadcrumb" {{ $attributes->class('text-label-sm uppercase text-on-surface-variant') }}>
        <ol class="flex flex-wrap items-center gap-2">
            @foreach ($items as $index => $item)
                <li class="flex items-center gap-2">
                    @if (! empty($item['url']) && ! $loop->last)
                        <a href="{{ $item['url'] }}" class="hover:text-oak-deep">{{ $item['label'] }}</a>
                    @else
                        <span class="text-oak-deep">{{ $item['label'] }}</span>
                    @endif

                    @unless ($loop->last)
                        <span aria-hidden="true">/</span>
                    @endunless
                </li>
            @endforeach
        </ol>
    </nav>
@endif
