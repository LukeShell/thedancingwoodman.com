<div>
    <x-storefront.section>
        <x-storefront.breadcrumb :items="$this->breadcrumbs" class="mb-10" />

        <div class="grid grid-cols-1 gap-x-4 lg:grid-cols-12">
            {{-- Gallery --}}
            <div
                class="space-y-4 lg:col-span-7"
                x-data="{
                    active: 0,
                    total: {{ count($this->imageUrls) }},
                    lightboxOpen: false,
                    touchStartX: null,
                    next() { if (this.total > 1) this.active = (this.active + 1) % this.total },
                    prev() { if (this.total > 1) this.active = (this.active - 1 + this.total) % this.total },
                    openLightbox() { if (this.total > 0) this.lightboxOpen = true },
                    closeLightbox() { this.lightboxOpen = false },
                    onTouchStart(e) { this.touchStartX = e.changedTouches[0].screenX },
                    onTouchEnd(e) {
                        if (this.touchStartX === null) return;
                        const dx = e.changedTouches[0].screenX - this.touchStartX;
                        if (dx > 40) this.prev();
                        else if (dx < -40) this.next();
                        this.touchStartX = null;
                    },
                }"
                x-effect="document.body.style.overflow = lightboxOpen ? 'hidden' : ''"
            >
                <div
                    class="relative aspect-square overflow-hidden bg-sapwood-cream"
                    @touchstart="onTouchStart($event)"
                    @touchend="onTouchEnd($event)"
                >
                    @if (! empty($this->imageUrls))
                        @foreach ($this->imageUrls as $i => $img)
                            <img
                                x-show="active === {{ $i }}"
                                @click="openLightbox()"
                                src="{{ $img['full'] }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full cursor-zoom-in object-cover transition-transform duration-700 hover:scale-105"
                            />
                        @endforeach

                        @if (count($this->imageUrls) > 1)
                            <button
                                type="button"
                                @click.stop="prev()"
                                aria-label="{{ __('Previous image') }}"
                                class="absolute left-4 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-sm bg-sapwood-cream/90 text-oak-deep shadow-sm transition-opacity hover:bg-sapwood-cream focus:outline-none focus:ring-1 focus:ring-oak-deep"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click.stop="next()"
                                aria-label="{{ __('Next image') }}"
                                class="absolute right-4 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-sm bg-sapwood-cream/90 text-oak-deep shadow-sm transition-opacity hover:bg-sapwood-cream focus:outline-none focus:ring-1 focus:ring-oak-deep"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div class="absolute bottom-4 right-4 bg-oak-deep/70 px-2 py-1 text-label-sm text-sapwood-cream">
                                <span x-text="active + 1"></span> / {{ count($this->imageUrls) }}
                            </div>
                        @endif
                    @else
                        <div class="flex h-full w-full items-center justify-center text-timber-ash">
                            <x-storefront.placeholder-image class="h-24 w-24" />
                        </div>
                    @endif
                </div>

                @if (count($this->imageUrls) > 1)
                    <div class="grid grid-cols-4 gap-4">
                        @foreach ($this->imageUrls as $i => $img)
                            <button
                                type="button"
                                @click="active = {{ $i }}"
                                class="aspect-square overflow-hidden border border-primary/10 bg-sapwood-cream"
                                :class="active === {{ $i }} ? 'border-oak-deep' : ''"
                            >
                                <img
                                    src="{{ $img['thumb'] }}"
                                    alt=""
                                    class="h-full w-full object-cover transition-opacity"
                                    :class="active === {{ $i }} ? 'opacity-100' : 'opacity-60 hover:opacity-100'"
                                />
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Lightbox --}}
                @if (! empty($this->imageUrls))
                    <div
                        x-show="lightboxOpen"
                        x-cloak
                        x-transition.opacity
                        @keydown.escape.window="closeLightbox()"
                        @keydown.arrow-left.window="if (lightboxOpen) prev()"
                        @keydown.arrow-right.window="if (lightboxOpen) next()"
                        @click="closeLightbox()"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-oak-deep/95 p-4 sm:p-12"
                        role="dialog"
                        aria-modal="true"
                    >
                        <button
                            type="button"
                            @click.stop="closeLightbox()"
                            aria-label="{{ __('Close') }}"
                            class="absolute right-4 top-4 flex h-11 w-11 items-center justify-center rounded-sm bg-sapwood-cream/90 text-oak-deep hover:bg-sapwood-cream focus:outline-none focus:ring-1 focus:ring-sapwood-cream"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div
                            class="relative flex h-full w-full max-w-6xl items-center justify-center"
                            @touchstart="onTouchStart($event)"
                            @touchend="onTouchEnd($event)"
                        >
                            @foreach ($this->imageUrls as $i => $img)
                                <img
                                    x-show="active === {{ $i }}"
                                    @click.stop
                                    src="{{ $img['full'] }}"
                                    alt="{{ $product->name }}"
                                    class="max-h-full max-w-full object-contain"
                                />
                            @endforeach

                            @if (count($this->imageUrls) > 1)
                                <button
                                    type="button"
                                    @click.stop="prev()"
                                    aria-label="{{ __('Previous image') }}"
                                    class="absolute left-0 top-1/2 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-sm bg-sapwood-cream/90 text-oak-deep hover:bg-sapwood-cream focus:outline-none focus:ring-1 focus:ring-sapwood-cream sm:-left-4"
                                >
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    @click.stop="next()"
                                    aria-label="{{ __('Next image') }}"
                                    class="absolute right-0 top-1/2 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-sm bg-sapwood-cream/90 text-oak-deep hover:bg-sapwood-cream focus:outline-none focus:ring-1 focus:ring-sapwood-cream sm:-right-4"
                                >
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-oak-deep/70 px-3 py-1 text-label-sm text-sapwood-cream">
                                    <span x-text="active + 1"></span> / {{ count($this->imageUrls) }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="flex flex-col lg:col-span-5">
                <div class="mb-4">
                    <span class="inline-flex items-center gap-2 bg-primary-container px-3 py-1 text-label-sm uppercase tracking-widest text-sapwood-cream">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ __('Handmade to Order') }}
                    </span>
                </div>

                <h1 class="mb-2 font-display text-headline-xl text-oak-deep">{{ $product->name }}</h1>

                <div class="mb-6 flex items-center gap-4">
                    <span class="font-display text-headline-md text-oak-deep">{{ $this->priceRange }}</span>
                    <div class="h-4 w-px bg-timber-ash/30"></div>
                    <div class="flex items-center gap-1">
                        <div class="flex text-brand-accent">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.363 1.118l1.286 3.957c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.196-1.539-1.118l1.286-3.957a1 1 0 00-.362-1.118L2.05 9.384c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.285-3.957z" />
                                </svg>
                            @endfor
                        </div>
                        <span class="text-label-sm text-secondary">({{ __(':n Reviews', ['n' => 48]) }})</span>
                    </div>
                </div>

                @if ($product->short_description)
                    <p class="mb-10 font-sans text-body-lg leading-relaxed text-on-surface-variant">
                        {{ $product->short_description }}
                    </p>
                @endif

                {{-- Per-attribute pickers --}}
                <div class="mb-10 space-y-8">
                    @foreach ($product->attributes as $attribute)
                        <div wire:key="attr-{{ $attribute->id }}">
                            <div class="mb-3 flex items-end justify-between">
                                <label class="font-sans text-label-md uppercase tracking-wider text-oak-deep">
                                    {{ __('Select') }} {{ $attribute->name }}
                                </label>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($attribute->values as $value)
                                    @php $selected = ($selectedValues[$attribute->id] ?? null) === $value->id; @endphp
                                    <button
                                        type="button"
                                        wire:click="selectValue({{ $attribute->id }}, {{ $value->id }})"
                                        wire:key="val-{{ $value->id }}"
                                        class="group relative overflow-hidden border p-3 text-left transition-colors outline-none focus:ring-1 focus:ring-oak-deep {{ $selected ? 'border-oak-deep' : 'border-timber-ash/30 hover:border-oak-deep' }}"
                                    >
                                        <span class="block font-sans text-label-md text-oak-deep">{{ $value->value }}</span>
                                        @if ($selected)
                                            <svg class="absolute right-1 top-1 h-4 w-4 text-oak-deep" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{-- Finish swatches --}}
                    @if ($product->finishes->isNotEmpty())
                        <div>
                            <label class="mb-4 block font-sans text-label-md uppercase tracking-wider text-oak-deep">
                                {{ __('Wood Finish') }}
                            </label>
                            <div class="flex flex-wrap gap-4">
                                @foreach ($product->finishes as $finish)
                                    @php $selected = $selectedFinishId === $finish->id; @endphp
                                    <button
                                        type="button"
                                        wire:click="selectFinish({{ $finish->id }})"
                                        wire:key="finish-{{ $finish->id }}"
                                        class="group flex cursor-pointer flex-col items-center gap-2"
                                        aria-pressed="{{ $selected ? 'true' : 'false' }}"
                                    >
                                        <span class="block h-12 w-12 rounded-full border-2 p-0.5 transition-all {{ $selected ? 'border-oak-deep' : 'border-transparent group-hover:border-oak-deep' }}">
                                            @if ($swatch = $finish->swatchUrl())
                                                <span class="block h-full w-full rounded-full bg-cover bg-center" style="background-image: url('{{ $swatch }}');"></span>
                                            @else
                                                <span class="block h-full w-full rounded-full" style="background-color: {{ $finish->hex_color ?? '#E8DCC4' }};"></span>
                                            @endif
                                        </span>
                                        <span class="text-label-sm {{ $selected ? 'font-bold text-oak-deep' : 'text-secondary' }}">
                                            {{ $finish->name }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="mb-12 flex flex-col gap-4 sm:flex-row">
                    <x-storefront.quantity-stepper
                        :value="$quantity"
                        decrement="decrement"
                        increment="increment"
                        :label="__('Quantity')"
                        class="h-14"
                    />

                    <button
                        type="button"
                        wire:click="addToBasket"
                        wire:loading.attr="disabled"
                        class="flex h-14 flex-1 items-center justify-center bg-oak-deep font-sans text-label-md uppercase tracking-widest text-sapwood-cream transition-opacity hover:opacity-90 active:scale-[0.98] disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="addToBasket">
                            {{ __('Add to Cart — :price', ['price' => $this->displayPrice]) }}
                        </span>
                        <span wire:loading wire:target="addToBasket">{{ __('Adding…') }}</span>
                    </button>
                </div>

                {{-- Trust badges --}}
                @if ($product->trustBadges->isNotEmpty())
                    <div class="grid grid-cols-2 gap-y-6 border-t border-timber-ash/20 pt-10">
                        @foreach ($product->trustBadges as $badge)
                            <div class="flex items-start gap-3" wire:key="badge-{{ $badge->id }}">
                                <flux:icon :name="$badge->icon" variant="outline" class="h-6 w-6 shrink-0 text-timber-ash" />
                                <div>
                                    <p class="text-label-md text-oak-deep">{{ $badge->title }}</p>
                                    <p class="text-label-sm text-secondary">{{ $badge->subtitle }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Philosophy / detail --}}
        @if ($product->description)
            <section class="mt-32">
                <div class="flex max-w-3xl flex-col space-y-6">
                    <p class="font-sans text-body-lg font-bold text-oak-deep">{{ __('Crafted for the Soul of the Home') }}</p>
                    <div class="prose prose-stone max-w-none font-sans text-body-lg text-on-surface-variant">
                        {!! $product->description !!}
                    </div>
                </div>
            </section>
        @endif

        {{-- Reviews (placeholder data — replace with real Review model) --}}
        <section class="mt-32 border-t border-timber-ash/20 pt-20">
            <div class="mb-16 flex flex-col items-start justify-between gap-8 md:flex-row md:items-end">
                <div>
                    <h2 class="mb-4 font-display text-headline-lg text-oak-deep">{{ __('Voice of the Workshop') }}</h2>
                    <div class="flex items-center gap-4">
                        <span class="font-display text-headline-md text-oak-deep">4.9/5</span>
                        <div class="flex text-brand-accent">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.363 1.118l1.286 3.957c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.196-1.539-1.118l1.286-3.957a1 1 0 00-.362-1.118L2.05 9.384c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.285-3.957z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                </div>
                <button type="button" class="border border-oak-deep bg-transparent px-8 py-4 font-sans text-label-md uppercase tracking-widest transition-all hover:bg-oak-deep hover:text-sapwood-cream">
                    {{ __('Write a Review') }}
                </button>
            </div>

            <div class="grid grid-cols-1 gap-gutter md:grid-cols-3">
                @foreach ([
                    ['title' => 'Exquisite Craftsmanship', 'body' => 'The wait was worth every second. The block is the center of our kitchen. You can feel the quality in every corner.', 'author' => 'Julianne R.'],
                    ['title' => 'Sturdy & Beautiful', 'body' => 'Truly built to last. It\'s incredibly heavy and doesn\'t move an inch while working. The finish is stunning.', 'author' => 'Mark T.'],
                    ['title' => 'Character & Soul', 'body' => 'Love the reclaimed wood aspect. You can see the history in the wood. It\'s a piece of art that we use every day.', 'author' => 'Sarah L.'],
                ] as $review)
                    <article class="relative bg-sapwood-cream p-8">
                        <div class="bg-grain absolute inset-0 opacity-30"></div>
                        <div class="relative">
                            <div class="mb-4 flex text-brand-accent">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.363 1.118l1.286 3.957c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.196-1.539-1.118l1.286-3.957a1 1 0 00-.362-1.118L2.05 9.384c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.285-3.957z" />
                                    </svg>
                                @endfor
                            </div>
                            <h3 class="mb-3 font-display text-headline-md text-oak-deep">{{ $review['title'] }}</h3>
                            <p class="mb-6 font-sans text-body-md italic text-on-surface-variant">“{{ $review['body'] }}”</p>
                            <p class="text-label-sm uppercase tracking-widest text-secondary">— {{ $review['author'] }} | {{ __('Verified Purchase') }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Related products --}}
        @if ($this->relatedProducts->isNotEmpty())
            <section class="mt-32">
                <h2 class="mb-12 text-center font-display text-headline-lg text-oak-deep">{{ __('Complete the Collection') }}</h2>
                <div class="grid grid-cols-1 gap-gutter md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($this->relatedProducts as $related)
                        <x-storefront.product-card :product="$related" wire:key="related-{{ $related->id }}" />
                    @endforeach
                </div>
            </section>
        @endif
    </x-storefront.section>
</div>
