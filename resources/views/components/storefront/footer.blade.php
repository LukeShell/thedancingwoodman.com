<footer class="mt-24 border-t border-timber-ash/10 bg-oak-deep pt-24 pb-12 text-sapwood-cream">
    <x-storefront.container as="div">
        <div class="mb-20 grid grid-cols-1 gap-gutter md:grid-cols-4">
            <div class="space-y-6">
                <p class="font-display text-headline-md tracking-tight text-sapwood-cream">
                    {{ __('The Dancing Woodman') }}
                </p>
                <p class="text-body-md leading-relaxed text-timber-ash">
                    {{ __('Handcrafted furniture with intention. Sustainably sourced, locally made, built for generations.') }}
                </p>
                <div class="flex gap-4 text-sapwood-cream">
                    <a href="#" aria-label="Facebook" class="transition-colors hover:text-timber-ash">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12a10 10 0 10-11.5 9.9v-7H8v-2.9h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12H16l-.4 2.9h-2.1v7A10 10 0 0022 12z"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram" class="transition-colors hover:text-timber-ash">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="5" stroke-width="1.8"/>
                            <circle cx="12" cy="12" r="4" stroke-width="1.8"/>
                            <circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Etsy" class="transition-colors hover:text-timber-ash">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M9 3h10v4M9 3v18h10v-4M9 12h7" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-label-sm font-bold uppercase tracking-[0.2em]">{{ __('Navigation') }}</h4>
                <ul class="space-y-3 text-label-md text-timber-ash">
                    <li><a href="{{ route('home') }}" class="transition-colors hover:text-sapwood-cream">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('shop.index') }}" class="transition-colors hover:text-sapwood-cream">{{ __('Shop') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('About') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Blog') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('FAQs') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Contact') }}</a></li>
                </ul>
            </div>

            <div class="space-y-6">
                <h4 class="text-label-sm font-bold uppercase tracking-[0.2em]">{{ __('Policy & Care') }}</h4>
                <ul class="space-y-3 text-label-md text-timber-ash">
                    <li><a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Sustainability') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Privacy Policy') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Shipping & Returns') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Support Guidelines') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Care & Maintenance') }}</a></li>
                </ul>
            </div>

            <div class="space-y-6">
                <h4 class="text-label-sm font-bold uppercase tracking-[0.2em]">{{ __('The Workshop') }}</h4>
                <div class="space-y-3 text-body-md text-timber-ash">
                    <p>
                        Unit 1, Bridge Farm Industries<br>
                        Botley Road, Curbridge<br>
                        Southampton, SO30 2HB
                    </p>
                    <p class="pt-4">P: 01489 795283</p>
                    <p>E: hello@thedancingwoodman.com</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center justify-between gap-6 border-t border-timber-ash/10 pt-12 md:flex-row">
            <p class="text-label-sm text-timber-ash">
                &copy; {{ date('Y') }} {{ __('The Dancing Woodman. Handcrafted with intention.') }}
            </p>
            <div class="flex gap-8 text-label-sm text-timber-ash">
                <a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Terms of Service') }}</a>
                <a href="#" class="transition-colors hover:text-sapwood-cream">{{ __('Cookie Policy') }}</a>
                <a href="https://lynetechnologies.com" target="_blank" rel="noopener" class="transition-colors hover:text-sapwood-cream">{{ __('Site by Lyne') }}</a>
            </div>
        </div>
    </x-storefront.container>
</footer>
