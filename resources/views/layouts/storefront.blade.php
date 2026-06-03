<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="storefront min-h-screen bg-sapwood-cream font-sans text-charcoal-text antialiased">
        <div class="bg-oak-deep px-4 py-2 text-center text-[10px] font-bold uppercase tracking-widest text-sapwood-cream md:text-label-sm">
            {{ __('Worldwide shipping available + free UK mainland delivery') }}
        </div>

        <x-storefront.header />

        <main class="min-h-[60vh]">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <x-storefront.footer />

        <script src="https://js.stripe.com/v3/"></script>
        @fluxScripts
    </body>
</html>
