<footer class="mt-24 border-t border-outline-variant/40 bg-sapwood-cream">
    <x-storefront.container as="div">
        <div class="flex flex-col gap-6 py-12 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-display text-headline-sm text-oak-deep">The Dancing Woodman</p>
                <p class="mt-2 text-body-sm text-on-surface-variant">
                    {{ __('Handmade rustic wooden furniture, crafted in the south of England.') }}
                </p>
            </div>
            <p class="text-label-sm uppercase text-on-surface-variant">
                &copy; {{ date('Y') }} {{ __('The Dancing Woodman') }}
            </p>
        </div>
    </x-storefront.container>
</footer>
