<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-stone-50 text-stone-900 antialiased dark:bg-stone-950 dark:text-stone-100">
        <header class="border-b border-stone-200 bg-white dark:border-stone-800 dark:bg-stone-900">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight">
                    The Dancing Woodman
                </a>
                <nav class="flex items-center gap-6 text-sm">
                    <a href="{{ route('home') }}" class="hover:text-amber-700 {{ request()->routeIs('home') ? 'font-medium' : '' }}">Home</a>
                    <a href="{{ route('shop.index') }}" class="hover:text-amber-700 {{ request()->routeIs('shop.*') ? 'font-medium' : '' }}">Shop</a>
                    <livewire:storefront.basket-icon />
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-amber-700">Dashboard</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-6 py-10">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <footer class="mt-16 border-t border-stone-200 bg-white py-8 dark:border-stone-800 dark:bg-stone-900">
            <div class="mx-auto max-w-6xl px-6 text-sm text-stone-500 dark:text-stone-400">
                &copy; {{ date('Y') }} The Dancing Woodman. Handmade rustic wooden furniture.
            </div>
        </footer>

        <script src="https://js.stripe.com/v3/"></script>
        @fluxScripts
    </body>
</html>
