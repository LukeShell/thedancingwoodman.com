<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="storefront min-h-screen bg-background font-sans text-charcoal-text antialiased">
        <header class="sticky top-0 z-50 flex h-20 items-center border-b border-timber-ash/30 bg-sapwood-cream">
            <x-storefront.container as="div">
                <div class="flex w-full items-center justify-between gap-8">
                    <a href="{{ route('home') }}" wire:navigate class="shrink-0 font-display text-headline-md tracking-tight text-oak-deep">
                        {{ __('The Dancing Woodman') }}
                    </a>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary" aria-hidden="true">lock</span>
                        <span class="text-label-sm uppercase tracking-widest text-secondary">{{ __('Secure Checkout') }}</span>
                    </div>
                </div>
            </x-storefront.container>
        </header>

        <main class="min-h-[60vh]">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <footer class="mt-20 bg-oak-deep px-margin-mobile py-12 text-sapwood-cream md:px-margin-desktop">
            <div class="mx-auto flex max-w-container-max flex-col items-center justify-between gap-8 md:flex-row">
                <div class="text-label-sm uppercase tracking-widest text-timber-ash">
                    &copy; {{ date('Y') }} {{ config('app.name', 'The Dancing Woodman') }}. {{ __('Handcrafted with intention.') }}
                </div>
                <div class="flex gap-8 text-label-sm">
                    <a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Privacy Policy') }}</a>
                    <a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Terms of Service') }}</a>
                    <a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Shipping Info') }}</a>
                </div>
            </div>
        </footer>

        <script src="https://js.stripe.com/v3/"></script>
        @fluxScripts
    </body>
</html>
