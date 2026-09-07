<x-artstory.layout :site-settings="$siteSettings" title="Artist Dashboard" active="artist_portal">
    <main class="mx-auto max-w-7xl px-6 py-12">
        <style>
            .artist-head { align-items: end; border-bottom: 1px solid #e5e7eb; display: flex; gap: 24px; justify-content: space-between; margin-bottom: 34px; padding-bottom: 26px; }
            .artist-kicker { color: #88884d; font-size: 12px; font-weight: 900; letter-spacing: .22em; margin-bottom: 12px; text-transform: uppercase; }
            .artist-title { font-family: "Playfair Display", serif; font-size: clamp(36px, 4vw, 58px); font-weight: 600; line-height: 1; }
            .artist-actions { align-items: center; display: flex; flex-wrap: wrap; gap: 12px; }
            .artist-btn { align-items: center; border: 1px solid #111827; display: inline-flex; font-size: 12px; font-weight: 900; justify-content: center; letter-spacing: .12em; min-height: 44px; padding: 0 18px; text-transform: uppercase; }
            .artist-btn--dark { background: #111827; color: #fff; }
            .artist-btn--light { background: #fff; color: #111827; }
            .artist-status { border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; font-size: 14px; line-height: 1.6; margin-bottom: 24px; padding: 14px 16px; }
            .artist-errors { border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; font-size: 14px; line-height: 1.6; margin-bottom: 24px; padding: 14px 16px; }
            .artist-overview { display: grid; gap: 28px; grid-template-columns: minmax(260px, .72fr) minmax(0, 1.28fr); }
            .artist-panel { border: 1px solid #e5e7eb; background: #fff; padding: 28px; }
            .artist-profile { align-items: center; display: grid; gap: 18px; grid-template-columns: 116px minmax(0, 1fr); }
            .artist-profile img,
            .artist-avatar { aspect-ratio: 1; background: #f3f4f6; object-fit: cover; width: 116px; }
            .artist-avatar { align-items: center; color: #9ca3af; display: grid; font-size: 32px; font-weight: 800; justify-items: center; }
            .artist-meta { color: #4b5563; display: grid; font-size: 14px; gap: 7px; line-height: 1.55; margin-top: 10px; }
            .artist-meta strong { color: #111827; }
            .artist-work { align-items: center; border-bottom: 1px solid #e5e7eb; display: grid; gap: 18px; grid-template-columns: 84px minmax(0, 1fr) auto; padding: 16px 0; }
            .artist-work img,
            .artist-work__placeholder { aspect-ratio: 1; background: #f3f4f6; object-fit: cover; width: 84px; }
            .artist-work__placeholder { display: grid; place-items: center; color: #9ca3af; }
            .artist-pill { background: #f3f4f6; color: #4b5563; display: inline-flex; font-size: 11px; font-weight: 800; letter-spacing: .08em; padding: 7px 10px; text-transform: uppercase; }
            @media (max-width: 960px) { .artist-head, .artist-overview { display: grid; grid-template-columns: 1fr; } .artist-actions { align-items: stretch; display: grid; } .artist-work { grid-template-columns: 74px minmax(0, 1fr); } .artist-pill { grid-column: 2; width: fit-content; } }
        </style>

        <header class="artist-head">
            <div>
                <p class="artist-kicker">Artist Dashboard</p>
                <h1 class="artist-title">{{ $artist->name }}</h1>
            </div>
            <div class="artist-actions">
                <a href="{{ route('artist.artworks.create') }}" class="artist-btn artist-btn--dark">Upload artwork</a>
                <a href="{{ route('artist.profile') }}" class="artist-btn artist-btn--light">Profile settings</a>
                <form method="POST" action="{{ route('artist.logout') }}">
                    @csrf
                    <button class="artist-btn artist-btn--light">Logout</button>
                </form>
            </div>
        </header>

        @if (session('status'))
            <div class="artist-status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="artist-errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="artist-overview">
            <section class="artist-panel">
                <p class="artist-kicker">Profile</p>
                <div class="artist-profile mt-6">
                    @if ($artist->pictureUrl())
                        <img src="{{ $artist->pictureUrl() }}" alt="{{ $artist->name }}">
                    @else
                        <div class="artist-avatar">{{ str($artist->name)->substr(0, 1)->upper() }}</div>
                    @endif
                    <div>
                        <h2 class="font-display text-2xl font-semibold text-black">{{ $artist->name }}</h2>
                        <div class="artist-meta">
                            <span><strong>Email:</strong> {{ $artist->email ?: '-' }}</span>
                            <span><strong>Phone:</strong> {{ $artist->phone ?: '-' }}</span>
                            <span><strong>Nationality:</strong> {{ $artist->nationality ?: '-' }}</span>
                            <span><strong>Birth place:</strong> {{ $artist->birth_place ?: '-' }}</span>
                        </div>
                    </div>
                </div>
                @if ($artist->biography)
                    <p class="mt-6 text-sm leading-7 text-gray-600">{{ $artist->biography }}</p>
                @endif
            </section>

            <section class="artist-panel">
                <div class="flex items-center justify-between gap-4">
                    <p class="artist-kicker mb-0">My Artworks</p>
                    <a href="{{ route('artist.artworks.create') }}" class="text-xs font-black uppercase tracking-[0.12em] text-[#88884d]">Add new</a>
                </div>
                <div class="mt-6">
                    @forelse ($artworks as $artwork)
                        <article class="artist-work">
                            @if ($artwork->imageUrl())
                                <img src="{{ $artwork->imageUrl() }}" alt="{{ $artwork->title }}">
                            @else
                                <div class="artist-work__placeholder">Image</div>
                            @endif
                            <div>
                                <h2 class="font-display text-xl font-semibold text-black">{{ $artwork->title }}</h2>
                                <p class="mt-2 text-sm text-gray-500">{{ $artwork->medium?->name ?: $artwork->artist_medium_name ?: 'Medium pending' }} · {{ $artwork->style?->name ?: $artwork->artist_style_name ?: 'Style pending' }}</p>
                            </div>
                            <span class="artist-pill">{{ $artwork->is_active ? 'Published' : 'In review' }}</span>
                        </article>
                    @empty
                        <div class="border border-dashed border-gray-300 p-10 text-center text-gray-500">No artworks uploaded yet.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
</x-artstory.layout>
