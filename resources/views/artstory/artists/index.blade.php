<x-artstory.layout :site-settings="$siteSettings" title="Artists" active="artists">
    <main class="mx-auto max-w-7xl px-6 py-16">
        <h1 class="mb-16 text-center font-display text-4xl font-light text-black">Our Artists</h1>

        @if ($artists->count())
            <div class="grid grid-cols-1 gap-12 md:grid-cols-3">
                @foreach ($artists as $artist)
                    <article class="border border-gray-200 p-8 text-center transition-shadow hover:shadow-lg">
                        <a href="{{ route('artists.show', $artist) }}" class="mx-auto mb-6 block size-32 overflow-hidden rounded-full bg-gray-200">
                            @if ($artist->pictureUrl())
                                <img src="{{ $artist->pictureUrl() }}" alt="{{ $artist->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="grid h-full w-full place-items-center font-display text-3xl text-black">{{ str($artist->name)->substr(0, 2)->upper() }}</span>
                            @endif
                        </a>
                        <h2 class="mb-2 font-display text-2xl font-light text-black">{{ $artist->name }}</h2>
                        <p class="mb-4 text-sm text-gray-500">{{ $artist->nationality ?: 'Contemporary Artist' }}</p>
                        <p class="mb-6 text-sm text-gray-600">{{ str($artist->biography ?: 'Explore biography and selected artworks from this ART Story artist.')->limit(95) }}</p>
                        <a href="{{ route('artists.show', $artist) }}" class="inline-block border border-gray-900 px-6 py-2 text-sm hover:bg-gray-100">View Artworks</a>
                    </article>
                @endforeach
            </div>
            <div class="mt-10">{{ $artists->links() }}</div>
        @else
            <div class="border border-gray-200 bg-gray-50 p-12 text-center">
                <h2 class="text-2xl font-light">No artists found</h2>
                <p class="mt-2 text-gray-600">Add active artists from the dashboard to display them here.</p>
            </div>
        @endif
    </main>
</x-artstory.layout>
