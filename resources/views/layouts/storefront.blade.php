<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="storefront min-h-screen bg-sapwood-cream font-sans text-charcoal-text antialiased">
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
