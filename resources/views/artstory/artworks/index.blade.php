<x-artstory.layout :site-settings="$siteSettings" title="Artworks" active="artworks">
    <style>
        .artwork-masonry {
            column-count: 1;
            column-gap: 2rem;
        }

        .artwork-masonry > article {
            break-inside: avoid;
            display: inline-block;
            margin-bottom: 2.5rem;
            width: 100%;
        }

        /* Preserve each original artwork ratio: no cropped or resized thumbnail frame. */
        .artwork-masonry > article > a > span {
            aspect-ratio: auto;
            display: block;
            margin-bottom: 1rem;
            padding: 0;
        }

        .artwork-masonry > article > a > span > img {
            display: block;
            height: auto;
            max-height: none;
            width: 100%;
        }

        /* Keep a large artist catalogue inside the filter panel, never below the footer. */
        .artwork-artist-filter {
            contain: paint;
            display: block;
            max-height: 12rem;
            overflow-x: hidden;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        @media (min-width: 640px) {
            .artwork-masonry {
                column-count: 2;
            }
        }

        @media (min-width: 1024px) {
            .artwork-masonry {
                column-count: 3;
            }
        }
    </style>

    <div class="artwork-catalog-page mx-auto max-w-7xl px-6 py-12">
        <div class="mb-10 flex flex-col gap-4 border-b border-gray-200 pb-8 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.22em] text-[#88884d]">Artworks</p>
                <h1 class="font-display text-5xl font-light leading-tight text-black">Artworks</h1>
            </div>
            <form method="GET" action="{{ route('artworks.index') }}" class="flex w-full max-w-md items-center border border-gray-300 bg-white px-3 py-2">
                <input type="hidden" name="currency" value="{{ $currency }}">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by artwork or artist" class="w-full bg-transparent text-sm outline-none">
                <button type="submit" aria-label="Search artworks">
                    <span class="material-symbols-rounded text-lg text-gray-500">search</span>
                </button>
            </form>
        </div>

        <div class="flex flex-col gap-8 md:flex-row">
            <aside class="h-fit w-full border border-gray-200 bg-gray-50 px-9 py-10 shadow-sm md:w-[31%] lg:w-1/4">
                <form method="GET" action="{{ route('artworks.index') }}" class="space-y-12">
                    <input type="hidden" name="price_min" value="0">
                    @if (request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <div>
                        <h3 class="mb-7 text-lg font-extrabold uppercase tracking-tight text-gray-950">Categories</h3>
                        <div class="space-y-5 text-xl text-gray-800">
                            <label class="filter-check">
                                <input type="checkbox" name="status" value="available" @checked(request('status') === 'available')>
                                <span class="box"></span>
                                <span class="label">Available</span>
                                <span class="count">{{ $statusCounts->get('available', 0) }}</span>
                            </label>
                            <label class="filter-check">
                                <input type="checkbox" name="status" value="sold" @checked(request('status') === 'sold')>
                                <span class="box"></span>
                                <span class="label">Sold</span>
                                <span class="count">{{ $statusCounts->get('sold', 0) }}</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-7 text-lg font-extrabold uppercase tracking-tight text-gray-950">Style</h3>
                        <div class="space-y-5 text-xl text-gray-800">
                            @forelse ($styles as $style)
                                <label class="filter-check">
                                    <input type="checkbox" name="style" value="{{ $style->id }}" @checked((string) request('style') === (string) $style->id)>
                                    <span class="box"></span>
                                    <span class="label">{{ $style->name }}</span>
                                    <span class="count">{{ $style->artworks_count }}</span>
                                </label>
                            @empty
                                <label class="filter-check opacity-50">
                                    <input type="checkbox" disabled>
                                    <span class="box"></span>
                                    <span class="label">Modern</span>
                                </label>
                                <label class="filter-check opacity-50">
                                    <input type="checkbox" disabled>
                                    <span class="box"></span>
                                    <span class="label">Abstract</span>
                                </label>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-7 text-lg font-extrabold uppercase tracking-tight text-gray-950">Artist</h3>
                        <div class="artwork-artist-filter space-y-5 text-xl text-gray-800">
                            @foreach ($artists as $artist)
                                <label class="filter-check">
                                    <input type="checkbox" name="artist" value="{{ $artist->id }}" @checked((string) request('artist') === (string) $artist->id)>
                                    <span class="box"></span>
                                    <span class="label">{{ $artist->name }}</span>
                                    <span class="count">{{ $artist->artworks_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-7 text-lg font-extrabold uppercase tracking-tight text-gray-950">Currency</h3>
                        <select name="currency" class="w-full border border-gray-300 bg-white p-3 text-sm outline-none">
                            <option value="BDT" @selected($currency === 'BDT')>BDT - Bangladesh</option>
                            <option value="USD" @selected($currency === 'USD')>USD - Other countries</option>
                        </select>
                    </div>

                    <div>
                        <h3 class="mb-7 text-lg font-extrabold uppercase tracking-tight text-gray-950">Price</h3>
                        <input data-price-range type="range" min="0" max="{{ $priceMax }}" step="{{ $currency === 'USD' ? 50 : 1000 }}" name="price_max" value="{{ request('price_max', min($priceMax, $currency === 'USD' ? 1000 : 10000)) }}" class="w-full accent-[#88884d]">
                        <div class="mt-7 text-xl text-gray-500">{{ $currency }} 0 - {{ $currency }} <span data-price-output>{{ number_format((int) request('price_max', min($priceMax, $currency === 'USD' ? 1000 : 10000))) }}</span></div>
                    </div>

                    <div>
                        <h3 class="mb-7 text-lg font-extrabold uppercase tracking-tight text-gray-950">Sort</h3>
                        <select name="sort" class="w-full border border-gray-300 bg-white p-3 text-sm outline-none">
                            <option value="">Newest first</option>
                            <option value="price_low" @selected(request('sort') === 'price_low')>Price low to high</option>
                            <option value="price_high" @selected(request('sort') === 'price_high')>Price high to low</option>
                        </select>
                    </div>

                    <button class="w-full bg-[#8f914f] py-5 text-xl text-white hover:opacity-90">Apply Filters</button>
                    <a href="{{ route('artworks.index') }}" class="block w-full text-center text-sm text-gray-500 hover:text-gray-900">Clear Filters</a>
                </form>
            </aside>

            <section
                class="w-full md:w-3/4"
                data-artwork-catalog
                data-next-page-url="{{ $artworks->nextPageUrl() }}"
                data-shown="{{ $artworks->lastItem() ?? 0 }}"
                data-total="{{ $artworks->total() }}"
            >
                <div class="mb-8 flex items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">{{ $artworks->total() }} artwork{{ $artworks->total() === 1 ? '' : 's' }} found</p>
                    <a href="{{ route('contact') }}" class="text-sm text-[#88884d] hover:underline">Need help choosing?</a>
                </div>
                @if ($artworks->count())
                    <div class="artwork-masonry" data-artwork-grid>
                        @include('artstory.artworks.partials.cards', ['artworks' => $artworks, 'currency' => $currency])
                    </div>
                    <div class="mx-auto mt-14 max-w-lg text-center" data-artwork-load-more-panel>
                        <p class="text-sm font-medium uppercase tracking-[0.08em] text-gray-500" data-artwork-load-more-status>
                            Showing {{ $artworks->lastItem() }} of {{ $artworks->total() }} artworks
                        </p>
                        <div class="mt-5 h-1.5 w-full bg-gray-200" aria-hidden="true">
                            <div
                                class="h-full bg-black transition-[width] duration-300"
                                data-artwork-load-more-progress
                                style="width: {{ $artworks->total() ? ($artworks->lastItem() / $artworks->total()) * 100 : 0 }}%"
                            ></div>
                        </div>
                        @if ($artworks->hasMorePages())
                            <button type="button" class="mt-8 border border-black bg-black px-12 py-4 text-base font-semibold uppercase tracking-[0.08em] text-white transition hover:bg-white hover:text-black disabled:cursor-wait disabled:opacity-60" data-artwork-load-more>
                                Load more
                            </button>
                        @endif
                        <p class="mt-4 text-sm text-red-700" data-artwork-load-more-feedback role="status" aria-live="polite"></p>
                    </div>
                @else
                    <div class="border border-gray-200 bg-gray-50 p-12 text-center">
                        <span class="material-symbols-rounded mb-4 text-4xl text-[#88884d]">filter_alt_off</span>
                        <h2 class="text-2xl font-light">No artworks found</h2>
                        <p class="mt-2 text-gray-600">Try changing the filters or add active artworks from the dashboard.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-artstory.layout>
