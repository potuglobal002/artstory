<x-artstory.layout :site-settings="$siteSettings" title="Virtual Gallery" active="virtual-galleries">
    <main class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:px-10">
        <div class="max-w-2xl">
            <p class="text-sm font-bold uppercase text-stone-500">ART Story virtual exhibitions</p>
            <h1 class="mt-3 font-display text-5xl text-stone-950 sm:text-6xl">Walk through the collection.</h1>
            <p class="mt-5 text-lg leading-8 text-stone-600">Explore curated rooms in an interactive 3D gallery. Select an artwork inside the space to view its full story.</p>
        </div>

        @if ($galleries->isEmpty())
            <div class="mt-12 border border-stone-200 bg-stone-50 p-8 text-stone-600">The first virtual exhibition is being prepared.</div>
        @else
            <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($galleries as $gallery)
                    <a href="{{ route('virtual-galleries.show', $gallery) }}" class="group block border border-stone-200 bg-white transition hover:border-stone-900">
                        <div class="aspect-[4/3] overflow-hidden bg-stone-900">
                            @if ($gallery->coverImageUrl())
                                <img src="{{ $gallery->coverImageUrl() }}" alt="{{ $gallery->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                            @else
                                <div class="flex h-full items-center justify-center bg-[radial-gradient(circle_at_50%_20%,#d6c4a4,transparent_38%),linear-gradient(145deg,#2c2823,#0c0b0a)] text-center font-display text-3xl text-white">{{ $gallery->name }}</div>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-4 text-xs font-bold uppercase text-stone-500"><span>{{ $gallery->rooms_count }} {{ \Illuminate\Support\Str::plural('room', $gallery->rooms_count) }}</span><span>Enter gallery</span></div>
                            <h2 class="mt-3 font-display text-3xl text-stone-950">{{ $gallery->name }}</h2>
                            @if ($gallery->description)
                                <p class="mt-3 line-clamp-3 text-sm leading-6 text-stone-600">{{ $gallery->description }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </main>
</x-artstory.layout>
