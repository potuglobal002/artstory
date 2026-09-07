@php
    $galleryImages = $exhibition->galleryImageUrls();
    $initialGalleryImages = array_slice($galleryImages, 0, 9);
    $remainingGalleryImages = array_slice($galleryImages, 9);
    $dateRange = collect([$exhibition->start_date?->format('d M Y'), $exhibition->end_date?->format('d M Y')])->filter()->unique()->implode(' - ');
@endphp

<x-artstory.layout :site-settings="$siteSettings" :title="$exhibition->title" active="exhibitions">
    <main>
        <section class="relative isolate min-h-[420px] overflow-hidden bg-[#171713] text-white md:min-h-[560px]">
            @if ($exhibition->backgroundImageUrl())
                <img src="{{ $exhibition->backgroundImageUrl() }}" alt="{{ $exhibition->title }}" class="absolute inset-0 -z-20 h-full w-full object-cover object-center" fetchpriority="high">
            @endif
            <div class="absolute inset-0 -z-10 bg-black/55"></div>
            <div class="mx-auto flex min-h-[420px] max-w-7xl flex-col justify-between px-6 py-8 md:min-h-[560px] md:py-12">
                <a href="{{ route('exhibitions.index') }}" class="inline-flex w-fit items-center gap-2 text-xs font-black uppercase tracking-[0.16em] text-white/90 transition hover:text-[#d2c576]"><span class="text-base">←</span> All exhibitions</a>
                <div class="max-w-4xl pb-4 md:pb-8"><p class="text-xs font-black uppercase tracking-[0.28em] text-[#d9cd81]">ART Story Exhibition @if ($dateRange)<span class="text-white/60">/</span> {{ $dateRange }}@endif</p><h1 class="mt-5 font-display text-4xl font-semibold leading-[1.04] text-white sm:text-5xl md:text-6xl lg:text-7xl">{{ $exhibition->title }}</h1>@if ($exhibition->excerpt)<p class="mt-6 max-w-2xl text-base leading-7 text-white/85 md:text-lg md:leading-8">{{ $exhibition->excerpt }}</p>@endif</div>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24"><div class="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-6 lg:grid-cols-[minmax(0,1fr)_280px] lg:gap-20"><article class="prose max-w-none prose-headings:font-display prose-headings:font-semibold prose-p:text-gray-700 prose-a:text-[#77773e]">{!! $exhibition->description !!}</article><aside class="h-fit border-t-4 border-[#8b884d] pt-6"><p class="text-xs font-black uppercase tracking-[0.22em] text-[#88884d]">Exhibition details</p>@if ($dateRange)<div class="mt-6 border-b border-gray-200 pb-5"><p class="text-xs font-bold uppercase tracking-[0.15em] text-gray-500">Dates</p><p class="mt-2 text-sm leading-6 text-gray-800">{{ $dateRange }}</p></div>@endif<div class="border-b border-gray-200 py-5"><p class="text-xs font-bold uppercase tracking-[0.15em] text-gray-500">Featured works</p><p class="mt-2 text-sm leading-6 text-gray-800">{{ $exhibition->artworks->count() }} selected artworks</p></div>@if ($exhibition->virtual_gallery_url)<a href="{{ $exhibition->virtual_gallery_url }}" class="mt-6 inline-flex items-center text-xs font-black uppercase tracking-[0.14em] text-black underline underline-offset-8">Visit virtual gallery <span class="ml-2 text-base">→</span></a>@endif @if ($exhibition->embedded_url)<a href="{{ $exhibition->embedded_url }}" class="mt-5 inline-flex items-center text-xs font-black uppercase tracking-[0.14em] text-black underline underline-offset-8">Open exhibition link <span class="ml-2 text-base">→</span></a>@endif</aside></div></section>

        @if ($exhibition->artworks->isNotEmpty())
            <section class="bg-[#f5f4f1] py-16 md:py-24"><div class="mx-auto max-w-7xl px-6"><div class="flex flex-col gap-4 border-b border-gray-300 pb-7 md:flex-row md:items-end md:justify-between"><div><p class="text-xs font-black uppercase tracking-[0.24em] text-[#88884d]">Selected catalogue</p><h2 class="mt-3 font-display text-4xl font-semibold text-black md:text-5xl">Works in the exhibition</h2></div><p class="max-w-sm text-sm leading-7 text-gray-600">Discover the artworks that shape this exhibition.</p></div><div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">@foreach ($exhibition->artworks as $artwork)@include('artstory.partials.artwork-card', ['artwork' => $artwork])@endforeach</div></div></section>
        @endif

        @if (count($galleryImages))
            <section class="bg-white py-16 md:py-24" data-exhibition-gallery><div class="mx-auto max-w-7xl px-6"><div class="flex flex-col gap-4 border-b border-gray-200 pb-7 md:flex-row md:items-end md:justify-between"><div><p class="text-xs font-black uppercase tracking-[0.24em] text-[#88884d]">Visual journal</p><h2 class="mt-3 font-display text-4xl font-semibold text-black md:text-5xl">Inside the exhibition</h2></div><p class="max-w-sm text-sm leading-7 text-gray-600">{{ count($galleryImages) }} images from the exhibition, studios, and opening moments.</p></div><div class="mt-10 columns-1 gap-5 sm:columns-2 lg:columns-3" data-exhibition-masonry>@foreach ($initialGalleryImages as $image)<figure data-gallery-item class="mb-5 break-inside-avoid overflow-hidden bg-[#f5f4f1]"><button type="button" data-gallery-image="{{ $image }}" aria-label="Open {{ $exhibition->title }} image" class="group block w-full overflow-hidden"><img src="{{ $image }}" alt="{{ $exhibition->title }}" class="h-auto w-full object-cover transition duration-500 group-hover:scale-[1.02]" loading="lazy"></button></figure>@endforeach</div>
                @if (count($remainingGalleryImages))
                    <template data-exhibition-gallery-more>@foreach ($remainingGalleryImages as $image)<figure data-gallery-item class="mb-5 break-inside-avoid overflow-hidden bg-[#f5f4f1]"><button type="button" data-gallery-image="{{ $image }}" aria-label="Open {{ $exhibition->title }} image" class="group block w-full overflow-hidden"><img src="{{ $image }}" alt="{{ $exhibition->title }}" class="h-auto w-full object-cover transition duration-500 group-hover:scale-[1.02]" loading="lazy"></button></figure>@endforeach</template><div class="mt-10 text-center"><button type="button" data-exhibition-load-more class="border border-black px-7 py-4 text-xs font-black uppercase tracking-[0.15em] text-black transition hover:bg-black hover:text-white">Load more images</button></div>
                @endif
            </div></section>
        @endif
    </main>

    @if (count($galleryImages))
        <dialog data-exhibition-lightbox class="m-0 h-screen max-h-none w-screen max-w-none bg-transparent p-0 backdrop:bg-black/85">
            <div class="relative grid h-full w-full place-items-center p-4 md:p-10">
                <button type="button" data-lightbox-close aria-label="Close image viewer" class="absolute right-5 top-5 z-10 grid h-11 w-11 place-items-center bg-white text-black transition hover:bg-[#d9cd81]"><span class="material-symbols-rounded">close</span></button>
                <button type="button" data-lightbox-previous aria-label="Previous image" class="absolute left-3 top-1/2 z-10 grid h-11 w-11 -translate-y-1/2 place-items-center bg-white/90 text-black transition hover:bg-[#d9cd81] md:left-7"><span class="material-symbols-rounded">arrow_back</span></button>
                <img data-lightbox-image src="" alt="" class="max-h-[82vh] max-w-full object-contain shadow-2xl">
                <button type="button" data-lightbox-next aria-label="Next image" class="absolute right-3 top-1/2 z-10 grid h-11 w-11 -translate-y-1/2 place-items-center bg-white/90 text-black transition hover:bg-[#d9cd81] md:right-7"><span class="material-symbols-rounded">arrow_forward</span></button>
                <p data-lightbox-counter class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-black/70 px-4 py-2 text-xs font-black uppercase tracking-[0.16em] text-white"></p>
            </div>
        </dialog>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-exhibition-gallery]').forEach((gallery) => {
                    const masonry = gallery.querySelector('[data-exhibition-masonry]');
                    const template = gallery.querySelector('[data-exhibition-gallery-more]');
                    const button = gallery.querySelector('[data-exhibition-load-more]');
                    const lightbox = document.querySelector('[data-exhibition-lightbox]');
                    const lightboxImage = lightbox?.querySelector('[data-lightbox-image]');
                    const counter = lightbox?.querySelector('[data-lightbox-counter]');
                    let activeIndex = 0;

                    if (!masonry || !lightbox || !lightboxImage || !counter) return;

                    const images = () => Array.from(masonry.querySelectorAll('[data-gallery-image]'));
                    const showImage = (index) => {
                        const galleryImages = images();
                        if (!galleryImages.length) return;
                        activeIndex = (index + galleryImages.length) % galleryImages.length;
                        const image = galleryImages[activeIndex];
                        lightboxImage.src = image.dataset.galleryImage;
                        lightboxImage.alt = image.querySelector('img')?.alt || '';
                        counter.textContent = `Image ${activeIndex + 1} of ${galleryImages.length}`;
                    };

                    masonry.addEventListener('click', (event) => {
                        const image = event.target.closest('[data-gallery-image]');
                        if (!image) return;
                        showImage(images().indexOf(image));
                        lightbox.showModal();
                    });

                    lightbox.querySelector('[data-lightbox-close]').addEventListener('click', () => lightbox.close());
                    lightbox.querySelector('[data-lightbox-previous]').addEventListener('click', () => showImage(activeIndex - 1));
                    lightbox.querySelector('[data-lightbox-next]').addEventListener('click', () => showImage(activeIndex + 1));
                    lightbox.addEventListener('click', (event) => { if (event.target === lightbox) lightbox.close(); });
                    document.addEventListener('keydown', (event) => {
                        if (!lightbox.open) return;
                        if (event.key === 'ArrowLeft') showImage(activeIndex - 1);
                        if (event.key === 'ArrowRight') showImage(activeIndex + 1);
                    });

                    if (template && button) button.addEventListener('click', () => {
                        Array.from(template.content.querySelectorAll('[data-gallery-item]')).slice(0, 9).forEach((item) => masonry.append(item));
                        if (!template.content.querySelector('[data-gallery-item]')) button.remove();
                    });
                });
            });
        </script>
    @endif
</x-artstory.layout>
