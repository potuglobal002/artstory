@php
    $exhibitionSection = $landingPage?->exhibition_section ?: \App\Models\LandingPage::defaults()['exhibition_section'];
    $heroImage = $landingPage?->exhibitionImageUrl() ?: asset('images/art-story-exhibition-event.jpg');
@endphp

<x-artstory.layout :site-settings="$siteSettings" title="Exhibitions" active="exhibitions">
    <main>
        <section class="bg-[#f5f4f1] py-20">
            <div class="mx-auto grid max-w-7xl grid-cols-1 items-end gap-12 px-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.28em] text-[#88884d]">{{ $exhibitionSection['eyebrow'] ?? 'Exhibitions' }}</p>
                    <h1 class="mt-5 font-display text-5xl font-semibold leading-tight text-black md:text-6xl">{{ $exhibitionSection['title'] ?? 'Curated stories from emerging artists.' }}</h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-gray-600">{{ $exhibitionSection['description'] ?? 'Explore focused selections from the ART Story catalogue, built to help collectors discover original works with context, confidence, and care.' }}</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('artworks.index') }}" class="inline-flex items-center gap-2 bg-black px-6 py-4 text-xs font-black uppercase tracking-[0.14em] text-white hover:bg-gray-800">
                            View Artworks
                            <span class="material-symbols-rounded text-lg">arrow_forward</span>
                        </a>
                        @if ($siteSettings?->events_pr_enabled ?? true)<a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 border border-black px-6 py-4 text-xs font-black uppercase tracking-[0.14em] text-black hover:bg-black hover:text-white">
                            Upcoming Events
                            <span class="material-symbols-rounded text-lg">calendar_month</span>
                        </a>@endif
                    </div>
                </div>
                <div class="relative">
                    <img src="{{ $heroImage }}" alt="ART Story exhibition artwork" class="aspect-[16/10] w-full object-cover shadow-2xl shadow-black/10">
                    <div class="absolute bottom-6 left-6 bg-white/90 px-6 py-5 backdrop-blur">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-gray-500">Current catalogue</p>
                        <p class="mt-2 text-2xl font-bold text-black">{{ number_format($artworkCount) }} artworks</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#f5f4f1] pb-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-8 border-b border-gray-200 pb-5"><p class="text-xs font-black uppercase tracking-[0.24em] text-gray-500">Curated programmes</p><h2 class="mt-2 font-display text-4xl font-semibold text-black">Current exhibitions</h2></div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($exhibitions as $exhibition)
                        <a href="{{ route('exhibitions.show', $exhibition) }}" class="group border border-gray-200 bg-white p-4 transition hover:-translate-y-1 hover:shadow-xl">
                            <div class="aspect-[4/3] overflow-hidden bg-gray-100">@if ($exhibition->backgroundImageUrl())<img src="{{ $exhibition->backgroundImageUrl() }}" alt="{{ $exhibition->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">@endif</div>
                            <p class="mt-5 text-xs font-black uppercase tracking-[0.18em] text-[#88884d]">{{ $exhibition->start_date?->format('d M Y') ?: 'Exhibition' }}</p>
                            <h3 class="mt-2 font-display text-2xl font-semibold text-black">{{ $exhibition->title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600">{{ $exhibition->excerpt ?: 'Explore this curated ART Story exhibition.' }}</p>
                            <p class="mt-4 text-xs font-black uppercase tracking-[0.14em] text-black">{{ $exhibition->artworks_count }} artworks <span class="ml-2">View details →</span></p>
                        </a>
                    @empty
                        <div class="col-span-full border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">Publish an exhibition from the admin panel to feature it here.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bg-white py-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-10 flex flex-col gap-4 border-b border-gray-200 pb-8 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.24em] text-gray-500">Featured exhibition</p>
                        <h2 class="mt-3 font-display text-4xl font-semibold text-black">New voices, original works</h2>
                    </div>
                    <p class="max-w-md text-sm leading-7 text-gray-600">A rotating selection based on active ART Story inventory and artists currently available on the platform.</p>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($featuredArtworks as $artwork)
                        <a href="{{ route('artworks.show', $artwork) }}" class="group border border-gray-200 bg-white p-4 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-black/10">
                            <span class="block aspect-[4/3] overflow-hidden bg-gray-100">
                                @if ($artwork->imageUrl())
                                    <img src="{{ $artwork->imageUrl() }}" alt="{{ $artwork->title ?: 'Artwork by ' . $artwork->artist->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <span class="grid h-full place-items-center font-display text-2xl text-gray-500">{{ $artwork->title ?: 'Untitled' }}</span>
                                @endif
                            </span>
                            <p class="mt-5 text-xs font-black uppercase tracking-[0.18em] text-[#88884d]">{{ $artwork->year ?: 'ART Story' }}</p>
                            <h3 class="mt-2 font-display text-2xl font-semibold leading-tight text-black">{{ $artwork->title ?: 'Untitled' }}</h3>
                            <p class="mt-2 text-sm font-semibold text-gray-600">{{ $artwork->artist->name }}</p>
                            <p class="mt-4 text-sm leading-6 text-gray-500">{{ $artwork->style?->name ?: 'Original artwork' }}{{ $artwork->medium?->name ? ' / ' . $artwork->medium->name : '' }}</p>
                        </a>
                    @empty
                        <div class="col-span-full border border-dashed border-gray-300 p-12 text-center text-gray-500">Add active artworks to show exhibition selections here.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</x-artstory.layout>
