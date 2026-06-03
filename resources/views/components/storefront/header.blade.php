<header class="border-b border-outline-variant/40 bg-sapwood-cream">
    <x-storefront.container as="div">
        <div class="flex items-center justify-between py-6">
            <a href="{{ route('home') }}" class="font-display text-headline-sm text-oak-deep">
                The Dancing Woodman
            </a>

            <nav class="flex items-center gap-8 text-label-md uppercase">
                <a href="{{ route('home') }}"
                   class="{{ request()->routeIs('home') ? 'text-oak-deep' : 'text-on-surface-variant hover:text-oak-deep' }}">
                    {{ __('Home') }}
                </a>
                <a href="{{ route('shop.index') }}"
                   class="{{ request()->routeIs('shop.*') ? 'text-oak-deep' : 'text-on-surface-variant hover:text-oak-deep' }}">
                    {{ __('Shop') }}
                </a>
                <livewire:storefront.basket-icon />
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-on-surface-variant hover:text-oak-deep">
                        {{ __('Dashboard') }}
                    </a>
                @endauth
            </nav>
        </div>
    </x-storefront.container>
</header>
