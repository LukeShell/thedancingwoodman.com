<div class="flex items-center">
    <a href="{{ route('basket.show') }}"
       class="relative inline-flex items-center transition-opacity hover:opacity-70 {{ request()->routeIs('basket.*') ? 'text-oak-deep' : 'text-oak-deep' }}"
       aria-label="{{ __('View basket') }}">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M16 11V7a4 4 0 10-8 0v4M5 9h14l-1.5 11a2 2 0 01-2 1.75H8.5a2 2 0 01-2-1.75L5 9z" />
        </svg>
        @if ($count > 0)
            <span class="absolute -right-2 -top-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-accent px-1.5 text-[10px] font-bold text-white">
                {{ $count }}
            </span>
        @endif
        <span class="sr-only">{{ __('Basket') }}</span>
    </a>
</div>
