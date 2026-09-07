<x-artstory.layout :site-settings="$siteSettings" :title="$artist->name" active="artists">
    <main class="mx-auto max-w-7xl px-6 py-16">
        <style>
            .artist-profile-hero { align-items: center; display: grid; gap: 56px; grid-template-columns: minmax(280px, 440px) minmax(0, 1fr); }
            .artist-profile-photo { background: #f7f7f4; border: 1px solid #e5e7eb; padding: 14px; }
            .artist-profile-photo img { aspect-ratio: 4 / 5; display: block; object-fit: cover; object-position: center top; width: 100%; }
            .artist-profile-placeholder { aspect-ratio: 4 / 5; align-items: center; background: #eef0f3; color: #000000; display: flex; font-family: "Playfair Display", serif; font-size: 48px; justify-content: center; }
            @media (max-width: 1024px) {
                .artist-profile-hero { grid-template-columns: 1fr; }
                .artist-profile-photo { max-width: 420px; }
            }
        </style>

        <a href="{{ route('artists.index') }}" class="text-sm text-gray-500 hover:text-gray-900">← Back to Artists</a>
        <section class="artist-profile-hero mt-8">
            <div class="artist-profile-photo">
                @if ($artist->pictureUrl())
                    <img src="{{ $artist->pictureUrl() }}" alt="{{ $artist->name }}">
                @else
                    <div class="artist-profile-placeholder">{{ str($artist->name)->substr(0, 2)->upper() }}</div>
                @endif
            </div>
            <div>
                <h1 class="mb-6 font-display text-5xl font-light text-black">{{ $artist->name }}</h1>
                <div class="mb-6 bg-gray-50 p-6 text-sm text-gray-600">
                    @if ($artist->nationality)<p><strong>Nationality:</strong> {{ $artist->nationality }}</p>@endif
                    @if ($artist->birth_place)<p><strong>Birth place:</strong> {{ $artist->birth_place }}</p>@endif
                    @if ($artist->date_of_birth)<p><strong>Life:</strong> {{ $artist->date_of_birth->format('Y') }}{{ $artist->date_of_death ? ' - ' . $artist->date_of_death->format('Y') : '' }}</p>@endif
                </div>
                <p class="leading-relaxed text-gray-700">{{ $artist->biography ?: 'This ART Story profile presents selected works, available artwork information, and collection details for the artist.' }}</p>
                <a href="{{ route('artworks.index', ['artist' => $artist->id]) }}" class="mt-8 inline-block bg-[#88884d] px-8 py-3 text-sm text-white hover:opacity-90">View Artworks</a>
            </div>
        </section>
        <section class="mt-20">
            <h2 class="mb-10 text-center font-display text-3xl font-light text-black">Artworks by {{ $artist->name }}</h2>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                @foreach ($artworks as $artwork)
                    @include('artstory.partials.artwork-card', ['artwork' => $artwork])
                @endforeach
            </div>
            <div class="mt-10">{{ $artworks->links() }}</div>
        </section>
    </main>
</x-artstory.layout>
