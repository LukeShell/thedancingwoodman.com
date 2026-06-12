<header class="sticky top-0 z-50 flex h-20 items-center border-b border-timber-ash/30 bg-sapwood-cream">
    <x-storefront.container as="div">
        <div class="flex w-full items-center justify-between gap-8">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="shrink-0 font-display text-headline-md tracking-tight text-oak-deep">
                    {{ __('The Dancing Woodman') }}
                </a>

                <nav class="hidden items-center gap-6 lg:flex">
                    <a href="{{ route('shop.index') }}"
                       class="font-sans text-label-md font-medium {{ request()->routeIs('shop.*') ? 'border-b-2 border-primary pb-1 font-bold text-primary' : 'text-secondary transition-colors hover:text-primary' }}">
                        {{ __('Shop') }}
                    </a>
                    <a href="#" class="font-sans text-label-md font-medium text-secondary transition-colors hover:text-primary">{{ __('About') }}</a>
                    <a href="#" class="font-sans text-label-md font-medium text-secondary transition-colors hover:text-primary">{{ __('Blog') }}</a>
                    <a href="#" class="font-sans text-label-md font-medium text-secondary transition-colors hover:text-primary">{{ __('FAQs') }}</a>
                    <a href="#" class="font-sans text-label-md font-medium text-secondary transition-colors hover:text-primary">{{ __('Showroom') }}</a>
                    <a href="#" class="font-sans text-label-md font-medium text-secondary transition-colors hover:text-primary">{{ __('Contact') }}</a>
                </nav>
            </div>

            <div class="flex items-center gap-6 text-oak-deep">
                <button type="button" aria-label="{{ __('Search') }}" class="transition-opacity hover:opacity-70">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />
                    </svg>
                </button>

                <livewire:storefront.basket-icon />

                <button type="button" aria-label="{{ __('Menu') }}" class="lg:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </x-storefront.container>
</header>
