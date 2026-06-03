<div>
    <a href="{{ route('basket.show') }}"
       class="relative inline-flex items-center gap-2 text-label-md uppercase transition {{ request()->routeIs('basket.*') ? 'text-oak-deep' : 'text-on-surface-variant hover:text-oak-deep' }}"
       aria-label="{{ __('View basket') }}">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 5h12m-9 3a1 1 0 11-2 0 1 1 0 012 0zm8 0a1 1 0 11-2 0 1 1 0 012 0z" />
        </svg>
        <span>{{ __('Basket') }}</span>
        @if ($count > 0)
            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-accent px-1.5 text-label-sm font-semibold text-white">
                {{ $count }}
            </span>
        @endif
    </a>
</div>
