@php
    $phone = preg_replace('/\D+/', '', $siteSettings?->whatsapp_phone ?: $siteSettings?->contact_phone ?: '');
    $galleryImages = $artwork->galleryImageUrls();
    $displayPrice = $artwork->displayPrice($currency ?? 'BDT');
@endphp

<x-artstory.layout :site-settings="$siteSettings" :title="$artwork->title ?: 'Artwork Details'" active="artworks">
    <main class="mx-auto max-w-7xl px-6 py-12">
        <style>
            .artwork-media-frame { background: #111; box-shadow: 0 18px 40px rgba(15, 23, 42, .14); overflow: hidden; }
            .artwork-media-frame img { opacity: 1; transform: translateX(0) scale(1); transition: opacity .28s ease, transform .28s ease; }
            .artwork-media-frame img.is-changing { opacity: 0; transform: translateX(18px) scale(.985); }
            .artwork-thumb { transition: border-color .22s ease, box-shadow .22s ease, transform .22s ease; }
            .artwork-thumb:hover,
            .artwork-thumb.is-active { border-color: #111827; box-shadow: 0 8px 20px rgba(15, 23, 42, .16); transform: translateY(-2px); }
            .artwork-thumb img { transition: opacity .22s ease; }
            .artwork-thumb:hover img,
            .artwork-thumb.is-active img { opacity: .92; }
            .artwork-details-panel { background: #f9fafb; margin-bottom: 32px; padding: 24px; }
            .artwork-details-inner { align-items: flex-start; display: grid; gap: 24px; grid-template-columns: minmax(0, 1fr) 126px; }
            .artwork-qr-card { background: #fff; border: 1px solid #e5e7eb; padding: 12px; text-align: center; }
            .artwork-qr-card img { display: block; height: 96px; margin: 0 auto; width: 96px; }
            .artwork-qr-card p { color: #6b7280; font-size: 10px; font-weight: 800; letter-spacing: .14em; margin: 8px 0 0; text-transform: uppercase; }
            @media (max-width: 560px) {
                .artwork-details-inner { grid-template-columns: 1fr; }
                .artwork-qr-card { max-width: 132px; }
            }
            @media (prefers-reduced-motion: reduce) {
                .artwork-media-frame img,
                .artwork-thumb,
                .artwork-thumb img { transition: none; }
            }
        </style>

        <div class="mb-8">
            <a href="{{ route('artworks.index', ['currency' => $currency ?? 'BDT']) }}" class="text-sm text-gray-500 hover:text-gray-900">← Back to Catalog</a>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            <div>
                @if (count($galleryImages))
                    <div class="artwork-media-frame flex min-h-[320px] items-center justify-center p-4 md:min-h-[460px]">
                        <img src="{{ $galleryImages[0] }}" alt="{{ $artwork->title ?: 'Artwork by ' . $artwork->artist->name }}" class="max-h-[72vh] w-full object-contain" data-artwork-main-image>
                    </div>
                @else
                    <div class="grid aspect-square place-items-center bg-gray-200 text-gray-500 shadow-lg">
                        <span class="font-display text-4xl">{{ $artwork->title ?: 'Artwork' }}</span>
                    </div>
                @endif
                @if (count($galleryImages) > 1)
                    <div class="mt-4 grid grid-cols-4 gap-4">
                        @foreach ($galleryImages as $index => $image)
                            <button type="button" @class(['artwork-thumb block border bg-white p-1', 'is-active' => $index === 0, 'border-gray-200' => $index !== 0]) data-artwork-thumb="{{ $image }}">
                                <img src="{{ $image }}" alt="" class="aspect-square w-full object-contain">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                @if ($artwork->artwork_code)
                    <p class="mb-3 inline-flex bg-gray-100 px-3 py-1 text-xs font-bold uppercase tracking-[.16em] text-gray-600">{{ $artwork->artwork_code }}</p>
                @endif
                <h1 class="mb-4 font-display text-4xl font-light uppercase text-black">{{ $artwork->title ?: 'Untitled artwork' }}</h1>
                <p class="mb-6 text-xl font-semibold">{{ $displayPrice ? ($currency ?? 'BDT') . ' ' . number_format($displayPrice, ($currency ?? 'BDT') === 'BDT' ? 0 : 2) : 'Price on request' }}</p>
                @if ($artwork->note)
                    <p class="mb-8 leading-relaxed text-gray-700">{{ $artwork->note }}</p>
                @endif

                <div class="artwork-details-panel">
                    <div class="artwork-details-inner">
                        <div>
                            <h3 class="mb-3 text-sm font-bold uppercase">Artwork Details</h3>
                            <p class="text-sm text-gray-600"><strong>Artist:</strong> <a href="{{ route('artists.show', $artwork->artist) }}" class="hover:text-[#88884d]">{{ $artwork->artist->name }}</a></p>
                            <p class="text-sm text-gray-600"><strong>Medium:</strong> {{ $artwork->medium?->name ?: 'Not specified' }}</p>
                            <p class="text-sm text-gray-600"><strong>Dimensions:</strong> {{ $artwork->size?->label() ?: 'Not specified' }}</p>
                            <p class="text-sm text-gray-600"><strong>Year:</strong> {{ $artwork->year ?: 'Not specified' }}</p>
                            <p class="text-sm text-gray-600"><strong>Canvases:</strong> {{ $artwork->canvas_count ?: 1 }}</p>
                            <p class="text-sm text-gray-600"><strong>Status:</strong> {{ str($artwork->status)->headline() }}</p>
                        </div>
                        @if ($artwork->artwork_code)
                            <div class="artwork-qr-card">
                                <img src="{{ route('artworks.qr', $artwork->artwork_code) }}" alt="QR code for {{ $artwork->artwork_code }}">
                                <p>{{ $artwork->artwork_code }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <a href="#artwork-inquiry-form" class="mb-6 inline-flex w-full items-center justify-center gap-2 bg-[#8b2e2e] px-12 py-4 text-lg text-white hover:opacity-90">
                    <svg aria-hidden="true" viewBox="0 0 24 24" class="h-6 w-6 fill-current"><path d="M12 2a9.9 9.9 0 0 0-8.58 14.85L2 22l5.3-1.39A10 10 0 1 0 12 2Zm0 18a8 8 0 0 1-4.08-1.12l-.29-.17-3.15.83.84-3.07-.19-.31A8 8 0 1 1 12 20Zm4.38-5.97c-.24-.12-1.42-.7-1.64-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1-.37-1.9-1.18-.7-.62-1.18-1.38-1.32-1.61-.14-.24-.02-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.41-.54-.42h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.69 2.58 4.1 3.62.57.25 1.02.4 1.37.51.58.18 1.11.15 1.53.09.47-.07 1.42-.58 1.62-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28Z"/></svg>
                    {{ $siteSettings?->whatsapp_inquiry_label ?: 'WhatsApp Inquiry' }}
                </a>

                <form id="artwork-inquiry-form" data-artwork-inquiry action="{{ route('artworks.inquiries.store', $artwork) }}" method="POST" class="border border-gray-200 p-6">
                    @csrf
                    <h3 class="mb-4 text-sm font-bold uppercase">Inquiry Form</h3>
                    <div class="mb-4 grid gap-4 md:grid-cols-2">
                        <input required name="name" placeholder="Full Name" class="border border-gray-300 p-3 outline-none focus:border-gray-900">
                        <input required name="whatsapp" placeholder="WhatsApp number" class="border border-gray-300 p-3 outline-none focus:border-gray-900">
                        <input name="email" type="email" placeholder="Email address" class="border border-gray-300 p-3 outline-none focus:border-gray-900">
                    </div>
                    <textarea required name="message" rows="4" class="mb-4 w-full border border-gray-300 p-3 outline-none focus:border-gray-900">I would like to know more about this artwork.</textarea>
                    <p data-inquiry-feedback class="mb-3 hidden text-sm"></p>
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 bg-[#88884d] px-8 py-3 text-sm text-white hover:opacity-90">
                        <svg aria-hidden="true" viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M12 2a9.9 9.9 0 0 0-8.58 14.85L2 22l5.3-1.39A10 10 0 1 0 12 2Zm0 18a8 8 0 0 1-4.08-1.12l-.29-.17-3.15.83.84-3.07-.19-.31A8 8 0 1 1 12 20Zm4.38-5.97c-.24-.12-1.42-.7-1.64-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1-.37-1.9-1.18-.7-.62-1.18-1.38-1.32-1.61-.14-.24-.02-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.41-.54-.42h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.69 2.58 4.1 3.62.57.25 1.02.4 1.37.51.58.18 1.11.15 1.53.09.47-.07 1.42-.58 1.62-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28Z"/></svg>
                        Send Inquiry on WhatsApp
                    </button>
                </form>
            </div>
        </div>

        @if ($relatedArtworks->count())
            <section class="mt-20">
                <h2 class="mb-10 text-center font-display text-3xl font-light text-black">You may also like</h2>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    @foreach ($relatedArtworks as $relatedArtwork)
                        @include('artstory.partials.artwork-card', ['artwork' => $relatedArtwork, 'currency' => $currency ?? 'BDT'])
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <script>
        document.querySelectorAll('[data-artwork-thumb]').forEach((button) => {
            button.addEventListener('click', () => {
                const mainImage = document.querySelector('[data-artwork-main-image]');

                if (! mainImage || mainImage.src === button.dataset.artworkThumb) {
                    return;
                }

                document.querySelectorAll('[data-artwork-thumb]').forEach((thumb) => thumb.classList.remove('is-active'));
                button.classList.add('is-active');
                mainImage.classList.add('is-changing');

                window.setTimeout(() => {
                    mainImage.src = button.dataset.artworkThumb;
                }, 170);

                mainImage.addEventListener('load', () => {
                    window.setTimeout(() => mainImage.classList.remove('is-changing'), 40);
                }, { once: true });
            });
        });
    </script>
</x-artstory.layout>
