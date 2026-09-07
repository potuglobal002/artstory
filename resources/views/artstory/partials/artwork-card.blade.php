@php
    $currency = $currency ?? 'BDT';
    $catalogCard = $catalogCard ?? false;
    $displayPrice = $artwork->displayPrice($currency);
    $showUrl = route('artworks.show', ['artwork' => $artwork, 'currency' => $currency]);
@endphp

<article class="group">
    <a href="{{ $showUrl }}" class="block">
        @if ($artwork->imageUrl())
            <span class="mb-4 flex aspect-[4/5] w-full items-center justify-center bg-[#f7f7f4] p-3">
                <img src="{{ $artwork->imageUrl() }}" alt="{{ $artwork->title ?: 'Artwork by ' . $artwork->artist->name }}" class="max-h-full w-full object-contain transition duration-300 group-hover:opacity-90">
            </span>
        @else
            <div class="mb-4 grid aspect-[4/5] w-full place-items-center bg-gray-200 p-6 text-center text-gray-500">
                <span class="font-display text-2xl">{{ $artwork->title ?: 'Artwork' }}</span>
            </div>
        @endif
    </a>
    <div class="mb-2 flex items-start justify-between gap-3">
        <h3 @class([
            'font-display font-semibold leading-tight text-black',
            'text-2xl' => $catalogCard,
            'text-lg' => ! $catalogCard,
        ])>{{ $artwork->title ?: 'Untitled artwork' }}</h3>
        <span class="material-symbols-rounded cursor-pointer text-xl text-gray-500">favorite</span>
    </div>
    @if ($catalogCard)
        <p class="mb-5 text-lg leading-7 text-gray-600">by {{ $artwork->artist->name }}</p>
    @else
        <p class="mb-4 text-sm leading-6 text-gray-500">
            Artist: {{ $artwork->artist->name }}<br>
            Size: {{ $artwork->size?->label() ?: 'N/A' }}<br>
            Technique: {{ $artwork->style?->name ?: 'N/A' }}
        </p>
    @endif
    @unless ($catalogCard)
        <div class="flex items-center justify-between gap-3">
            <span class="text-lg font-semibold">{{ $displayPrice ? $currency . ' ' . number_format($displayPrice, $currency === 'BDT' ? 0 : 2) : 'On request' }}</span>
            <a href="{{ $showUrl }}" class="bg-[#88884d] px-4 py-2 text-xs text-white hover:opacity-90">Inquiry</a>
        </div>
    @endunless
</article>
