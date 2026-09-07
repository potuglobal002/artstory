@php
    $landingPage ??= null;
    $tagline = $landingPage?->hero_subtitle ?: $siteSettings?->tagline ?: '';
    $heroTitle = $landingPage?->hero_title ?: 'Every Art has a story. Let us be part of yours.';
    $heroTitleParts = preg_split('/[.|\\n]+/', $heroTitle, 2, PREG_SPLIT_NO_EMPTY);
    $collageImage = $landingPage?->aboutImageUrl() ?: asset('images/art-story-collage.jpg');
    $heroVisual = $landingPage?->heroBackgroundUrl() ?: asset('images/art-story-hero-brochure.jpg');
    $exhibitionSection = $landingPage?->exhibition_section ?: \App\Models\LandingPage::defaults()['exhibition_section'];
    $exhibitionEventImage = $landingPage?->exhibitionImageUrl() ?: asset('images/art-story-exhibition-event.jpg');
    $storySections = collect($landingPage?->services ?: \App\Models\LandingPage::defaults()['services']);
    $aboutSection = $storySections->firstWhere('accent', 'about') ?: $storySections->first();
    $goalSection = $storySections->firstWhere('accent', 'goal');
    $problemSection = $storySections->firstWhere('accent', 'problem');
    $csrSection = $storySections->firstWhere('accent', 'csr');
    $offerSection = $storySections->firstWhere('accent', 'offer');
    $featuredLabels = collect($landingPage?->programs ?: \App\Models\LandingPage::defaults()['programs']);
    $artworkLabel = $featuredLabels->firstWhere('title', 'Featured Artworks') ?: $featuredLabels->first();
    $artistLabel = $featuredLabels->firstWhere('title', 'Featured Artists') ?: $featuredLabels->skip(1)->first();
    $images = [
        $collageImage,
        'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1579783901586-d88db74b4fe4?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1578301978693-85fa9c0320b9?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1578926288207-a90a5366759d?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1554188248-986adbb73be4?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1549490349-8643362247b5?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1578321272176-b7bbc0679853?auto=format&fit=crop&w=900&q=85',
        'https://images.unsplash.com/photo-1574182245530-967d9b3831af?auto=format&fit=crop&w=900&q=85',
    ];
    $sampleTitles = ['Still Life', 'Colour Table', 'Memory Field', 'City Bird'];
    $heroArtworks = $featuredArtworks->values();
@endphp

<x-artstory.layout :site-settings="$siteSettings" title="Home" active="home" :immersive-header="true">
    <main class="mx-auto max-w-7xl px-6 pb-20 pt-0">
        <style>
            .landing-flow { color: #111; }
            .landing-kicker { color: #6b7280; font-size: 12px; font-weight: 800; letter-spacing: .24em; text-transform: uppercase; }
            .landing-title { color: #111; font-family: "Playfair Display", serif; font-size: clamp(30px, 3.3vw, 46px); font-weight: 600; letter-spacing: 0; line-height: 1.12; }
            .landing-quote { border-left: 3px solid #111; color: #111; font-family: "Playfair Display", serif; font-size: clamp(22px, 2.2vw, 32px); font-weight: 600; line-height: 1.28; margin-top: 34px; padding-left: 22px; }
            .landing-copy { color: #5f6673; font-size: 16px; line-height: 1.9; }
            .landing-rule { border-top: 0; }
            .landing-btn { align-items: center; border: 1px solid #111; color: #111; display: inline-flex; font-size: 12px; font-weight: 800; gap: 8px; letter-spacing: .12em; padding: 13px 18px; text-transform: uppercase; transition: .2s ease; }
            .landing-btn:hover { background: #111; color: #fff; }
            .art-cover-hero { --hero-parallax-y: 0px; background-image: linear-gradient(90deg, rgba(255,255,255,.98) 0 24%, rgba(255,255,255,.82) 41%, rgba(255,255,255,.22) 62%, rgba(255,255,255,.02) 100%), var(--hero-art-image) !important; background-position: center calc(50% + var(--hero-parallax-y)), center calc(50% + var(--hero-parallax-y)); background-size: cover; isolation: isolate; margin-left: calc(50% - 50vw); margin-right: calc(50% - 50vw); overflow: hidden; padding: 130px max(24px, calc((100vw - 1380px) / 2)) 44px !important; position: relative; transition: background-position .08s linear; }
            .art-cover-hero::before { background: linear-gradient(180deg, rgba(255,255,255,0) 68%, #fff 100%); content: "" !important; inset: 0 !important; opacity: 1 !important; position: absolute !important; transform: none !important; width: auto !important; z-index: -1 !important; }
            .art-cover-hero::after { display: none !important; }
            .cover-hero__inner { display: block; min-height: 350px; position: relative; z-index: 2; }
            .cover-hero__copy { align-content: center; display: grid; max-width: 500px; min-height: 320px; padding: 18px 0; }
            .cover-hero__copy > * { animation: hero-rise .72s cubic-bezier(.2,.75,.2,1) both; }
            .cover-hero__copy > *:nth-child(2) { animation-delay: .08s; }
            .cover-hero__copy > *:nth-child(3) { animation-delay: .16s; }
            .cover-hero__copy > *:nth-child(4) { animation-delay: .24s; }
            .cover-hero__kicker { color: #6b7280; display: inline-flex; font-size: 11px; font-weight: 900; letter-spacing: .2em; margin-bottom: 22px; text-transform: uppercase; }
            .cover-hero__copy h1 { color: #111; font-family: "Playfair Display", Georgia, serif; font-size: clamp(40px, 4.2vw, 62px); font-weight: 650; letter-spacing: 0; line-height: .96; max-width: 500px; }
            .cover-hero__copy h1 span,
            .cover-hero__copy h1 em { display: block; }
            .cover-hero__copy h1 em { color: #111; font-style: normal; font-weight: 500; }
            .cover-hero__copy p { color: #4b5563; font-size: 16px; line-height: 1.75; margin-top: 22px; max-width: 420px; }
            .cover-hero__actions { align-items: center; display: flex; flex-wrap: wrap; gap: 14px; margin-top: 32px; }
            .cover-hero__primary { align-items: center; background: #111; border-radius: 999px; color: #fff; display: inline-flex; font-size: 11px; font-weight: 900; gap: 8px; letter-spacing: .1em; min-height: 46px; padding: 0 22px; text-transform: uppercase; transition: .2s ease; }
            .cover-hero__primary:hover { background: #303030; transform: translateY(-2px); }
            .cover-hero__secondary { color: #111; font-size: 11px; font-weight: 900; letter-spacing: .12em; text-transform: uppercase; }
            .cover-hero__art { display: none; }
            .story-intro { display: grid; gap: 56px; grid-template-columns: minmax(320px, .82fr) minmax(0, 1.18fr); padding: 54px 0 72px; }
            .story-intro__image { aspect-ratio: 4 / 5; max-height: 560px; overflow: hidden; background: #f4f4f0; }
            .story-intro__image img { height: 100%; object-fit: cover; width: 100%; }
            .story-points { display: grid; gap: 16px; grid-template-columns: repeat(3, minmax(0, 1fr)); margin-top: 38px; }
            .story-point { background: #fafafa; border: 1px solid #e8e8e8; padding: 22px; }
            .story-point:last-child { border-right: 0; padding-right: 0; }
            .story-point h3 { color: #111; font-size: 12px; font-weight: 800; letter-spacing: .11em; margin-bottom: 12px; text-transform: uppercase; }
            .story-point p { color: #6b7280; font-size: 13px; line-height: 1.75; }
            .split-feature { display: grid; gap: 1px; grid-template-columns: repeat(2, minmax(0, 1fr)); margin-bottom: 96px; }
            .split-feature article { min-height: 310px; padding: 42px; }
            .split-feature article:first-child { background: #111; color: #fff; }
            .split-feature article:last-child { background: #f6f6f3; }
            .split-feature article:first-child .landing-kicker,
            .split-feature article:first-child .landing-copy { color: rgba(255,255,255,.68); }
            .split-feature article:first-child .landing-title { color: #fff; }
            .section-head { align-items: end; border-bottom: 1px solid #dedede; display: flex; gap: 24px; justify-content: space-between; margin-bottom: 40px; padding-bottom: 28px; }
            .art-grid { display: grid; gap: 28px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .art-card { background: #fff; border: 1px solid #e4e4e0; display: flex; flex-direction: column; min-height: 100%; transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease; }
            .art-card:hover { border-color: #cfcfc8; box-shadow: 0 20px 46px rgba(17,17,17,.08); transform: translateY(-3px); }
            .art-card__image { aspect-ratio: 1 / .86; background: #f3f4f6; display: block; overflow: hidden; }
            .art-card__image img { height: 100%; object-fit: cover; transition: transform .45s ease; width: 100%; }
            .art-card:hover .art-card__image img { transform: scale(1.04); }
            .art-card__body { display: flex; flex: 1; flex-direction: column; padding: 18px; }
            .art-card__title-row { align-items: start; display: flex; gap: 14px; justify-content: space-between; }
            .art-card h3 { color: #33436b; font-family: "Playfair Display", serif; font-size: 21px; font-weight: 600; line-height: 1.2; }
            .art-card__heart { color: #697386; flex: 0 0 auto; font-size: 23px; line-height: 1; }
            .art-card__artist { color: #6b7280; font-size: 14px; font-weight: 700; margin-top: 12px; }
            .art-card__meta { color: #6b7280; font-size: 13px; line-height: 1.65; margin-top: 6px; }
            .art-card__meta span { display: block; }
            .art-card__foot { align-items: center; display: flex; gap: 14px; justify-content: space-between; margin-top: auto; padding-top: 20px; }
            .art-card__price { color: #111827; font-size: 17px; font-weight: 900; }
            .art-card__price small { color: #6b7280; display: block; font-size: 12px; font-weight: 700; margin-bottom: 2px; }
            .art-card__link { background: #898b55; color: #fff; display: inline-flex; font-size: 12px; font-weight: 800; justify-content: center; min-width: 78px; padding: 11px 16px; transition: background .2s ease; }
            .art-card__link:hover { background: #111; color: #fff; }
            .artists-showcase { border-top: 1px solid #dedede; display: grid; gap: 64px; grid-template-columns: minmax(280px, .7fr) minmax(0, 1.3fr); padding-top: 76px; }
            .artist-list { border-top: 1px solid #111; }
            .artist-card { align-items: center; border-bottom: 1px solid #e5e5e5; color: #111; display: grid; gap: 22px; grid-template-columns: 68px minmax(0, 1fr) auto; padding: 22px 0; transition: .2s ease; }
            .artist-card:hover { padding-left: 14px; }
            .artist-card img,
            .artist-card__initial { aspect-ratio: 1 / 1; border-radius: 999px; height: 68px; object-fit: cover; width: 68px; }
            .artist-card__initial { align-items: center; background: #111; color: #fff; display: grid; font-size: 12px; font-weight: 800; justify-items: center; letter-spacing: .14em; }
            .artist-card h3 { font-family: "Playfair Display", serif; font-size: 24px; font-weight: 600; line-height: 1.2; }
            .artist-card p { color: #6b7280; font-size: 13px; margin-top: 7px; }
            .artist-card__arrow { align-items: center; border: 1px solid #d1d5db; border-radius: 999px; display: grid; height: 38px; justify-items: center; transition: .2s ease; width: 38px; }
            .artist-card:hover .artist-card__arrow { background: #111; border-color: #111; color: #fff; }
            .exhibition-preview { background: #111; color: #fff; display: grid; gap: 1px; grid-template-columns: minmax(0, 1fr) minmax(280px, .72fr); margin: 0 0 64px; max-height: 285px; overflow: hidden; }
            .exhibition-preview__copy { padding: 26px 32px; }
            .exhibition-preview__copy .landing-kicker,
            .exhibition-preview__copy .landing-copy { color: rgba(255,255,255,.7); }
            .exhibition-preview__copy .landing-title { color: #fff; }
            .exhibition-preview__media { background: #f4f4f0; display: block; min-height: 220px; overflow: hidden; position: relative; }
            .exhibition-preview__media img { height: 100%; inset: 0; object-fit: cover; position: absolute; width: 100%; }
            .exhibition-preview__actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
            .exhibition-preview__actions a { align-items: center; display: inline-flex; font-size: 10px; font-weight: 900; gap: 7px; letter-spacing: .1em; min-height: 34px; padding: 0 13px; text-transform: uppercase; transition: .2s ease; }
            .exhibition-preview__actions a:first-child { background: #fff; color: #111; }
            .exhibition-preview__actions a:last-child { border: 1px solid rgba(255,255,255,.5); color: #fff; }
            .exhibition-preview__actions a:hover { transform: translateY(-2px); }
            @keyframes hero-rise {
                from { opacity: 0; transform: translateY(18px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @media (max-width: 1024px) {
                .story-intro,
                .exhibition-preview,
                .split-feature,
                .artists-showcase { grid-template-columns: 1fr; }
                .art-cover-hero { background-image: linear-gradient(90deg, rgba(255,255,255,.98) 0 38%, rgba(255,255,255,.62) 68%, rgba(255,255,255,.12) 100%), var(--hero-art-image) !important; }
                .cover-hero__inner { grid-template-columns: 1fr; min-height: auto; }
                .cover-hero__copy { max-width: 620px; min-height: auto; }
                .art-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }
            @media (max-width: 640px) {
                .art-cover-hero { border-bottom: 0 !important; box-shadow: none !important; background-image: linear-gradient(90deg, rgba(255,255,255,.94) 0 30%, rgba(255,255,255,.66) 58%, rgba(255,255,255,.24) 100%), var(--hero-art-image) !important; background-position: center top, center top; padding: 132px 24px 42px !important; }
                .art-cover-hero::after { display: none !important; }
                .landing-flow,
                .landing-rule { border-top: 0 !important; }
                .cover-hero__copy { padding: 0; }
                .cover-hero__copy h1 { font-size: clamp(34px, 10vw, 48px); text-shadow: 0 2px 18px rgba(255,255,255,.96); }
                .cover-hero__copy p,
                .cover-hero__kicker,
                .cover-hero__secondary { text-shadow: 0 1px 14px rgba(255,255,255,.92); }
                .cover-hero__feature h2 { font-size: 21px; }
                .section-head { align-items: start; flex-direction: column; }
                .story-intro { gap: 28px; padding: 38px 0 48px; }
                .story-intro__image { aspect-ratio: 16 / 10; max-height: 240px; }
                .story-points { border-top: 1px solid #e5e7eb; gap: 0; margin-top: 26px; }
                .story-points,
                .art-grid,
                .artist-list { grid-template-columns: 1fr; }
                .story-point { background: transparent; border: 0; border-bottom: 1px solid #e5e7eb; padding: 18px 0; }
                .story-point:last-child { border-bottom: 0; }
                .story-point h3 { margin-bottom: 8px; }
                .story-point p { font-size: 13px; line-height: 1.65; }
                .exhibition-preview { display: flex; flex-direction: column; margin-bottom: 54px; max-height: none; overflow: visible; }
                .exhibition-preview__copy { padding: 28px 24px 30px; }
                .exhibition-preview__copy .landing-title { font-size: 28px !important; line-height: 1.15; }
                .exhibition-preview__copy .landing-copy { font-size: 14px !important; line-height: 1.65 !important; }
                .exhibition-preview__media { aspect-ratio: 16 / 8.5; min-height: 0; }
                .exhibition-preview__actions { gap: 8px; }
                .exhibition-preview__actions a { flex: 1 1 150px; justify-content: center; }
                .split-feature article { min-height: auto; padding: 32px 24px; }
            }
            @media (prefers-reduced-motion: reduce) {
                .art-cover-hero { transition: none; }
                .cover-hero__copy > *,
                .site-reveal,
                .site-reveal-child { animation: none !important; opacity: 1 !important; transform: none !important; transition: none !important; }
            }
        </style>

        <section class="art-cover-hero" style="--hero-art-image: url('{{ $heroVisual }}');">
            <div class="cover-hero__inner">
                <div class="cover-hero__copy">
                    <span class="cover-hero__kicker">ART Story</span>
                    <h1>
                        <span>{{ trim($heroTitleParts[0] ?? $heroTitle) }}.</span>
                        <em>{{ trim($heroTitleParts[1] ?? 'Let us be part of yours') }}</em>
                    </h1>
                    @if (filled($tagline))
                        <p>{{ $tagline }}</p>
                    @endif
                    <div class="cover-hero__actions">
                        <a href="{{ $landingPage?->hero_cta_url ?: route('artworks.index') }}" class="cover-hero__primary">
                            <span>{{ $landingPage?->hero_cta_label ?: 'Start Exploring' }}</span>
                            <span class="material-symbols-rounded">arrow_forward</span>
                        </a>
                        <a href="#featured-artworks" class="cover-hero__secondary">View collection</a>
                    </div>
                </div>

            </div>
        </section>

        <div class="landing-flow">
            <section class="landing-rule">
                <div class="story-intro">
                    <div class="story-intro__image">
                        <img src="{{ $collageImage }}" alt="ART Story artwork collage">
                    </div>

                    <div>
                        <span class="landing-kicker">About ART Story</span>
                        <h2 id="about-art-story" class="landing-title" style="margin-top: 18px;">{{ $aboutSection['title'] ?? 'About Us' }}</h2>
                        <p class="landing-copy" style="margin-top: 22px;">{{ $aboutSection['description'] ?? '' }}</p>
                        <p class="landing-quote">{{ $tagline }}</p>

                        <div class="story-points">
                            <article class="story-point">
                                <h3>{{ $goalSection['title'] ?? 'Our Goal' }}</h3>
                                <p>{{ $goalSection['description'] ?? 'Empower local talents and create global career opportunities in art.' }}</p>
                            </article>
                            <article class="story-point">
                                <h3>{{ $problemSection['title'] ?? 'Problem ART Story Solves' }}</h3>
                                <p>{{ $problemSection['description'] ?? 'Support artists who need visibility, exhibitions, and collector reach.' }}</p>
                            </article>
                            <article class="story-point">
                                <h3>{{ $csrSection['title'] ?? 'Our CSR' }}</h3>
                                <p>{{ $csrSection['description'] ?? 'Part of proceeds are pledged toward non-profit work and giving back.' }}</p>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            @if (($siteSettings?->exhibition_enabled ?? true) || ($siteSettings?->events_pr_enabled ?? true))
            <section class="exhibition-preview">
                <article class="exhibition-preview__copy">
                    <span class="landing-kicker">{{ $exhibitionSection['eyebrow'] ?? 'Exhibitions / Upcoming Events' }}</span>
                    <h2 class="landing-title" style="font-size: clamp(24px, 2.4vw, 34px); margin-top: 10px;">{{ $exhibitionSection['title'] ?? 'Discover ART Story beyond the catalogue.' }}</h2>
                    <p class="landing-copy" style="font-size: 14px; line-height: 1.65; margin-top: 12px;">{{ $exhibitionSection['description'] ?? 'Explore curated selections, upcoming previews, and collector-focused moments around emerging artists.' }}</p>
                    <div class="exhibition-preview__actions">
                        @if ($siteSettings?->exhibition_enabled ?? true)<a href="{{ route('exhibitions.index') }}">
                            {{ $exhibitionSection['exhibition_cta_label'] ?? 'Exhibitions' }}
                            <span class="material-symbols-rounded" style="font-size: 18px;">arrow_forward</span>
                        </a>@endif
                        @if ($siteSettings?->events_pr_enabled ?? true)<a href="{{ route('events.index') }}">
                            {{ $exhibitionSection['event_cta_label'] ?? 'Upcoming Events' }}
                            <span class="material-symbols-rounded" style="font-size: 18px;">calendar_month</span>
                        </a>@endif
                    </div>
                </article>
                @if ($siteSettings?->exhibition_enabled ?? true)<a href="{{ route('exhibitions.index') }}" class="exhibition-preview__media">
                    <img src="{{ $exhibitionEventImage }}" alt="Nikhil Shristhi exhibition preview">
                </a>@endif
            </section>
            @endif

            <section id="featured-artworks" style="margin-bottom: 104px;">
                <div class="section-head">
                    <div>
                        <span class="landing-kicker">{{ $artworkLabel['category'] ?? 'Selected Works' }}</span>
                        <h2 class="landing-title" style="margin-top: 14px;">{{ $artworkLabel['title'] ?? 'Featured Artworks' }}</h2>
                        @if (! blank($artworkLabel['description'] ?? null))
                            <p class="landing-copy" style="max-width: 620px; margin-top: 16px;">{{ $artworkLabel['description'] }}</p>
                        @endif
                    </div>
                    <a href="{{ $artworkLabel['url'] ?? route('artworks.index') }}" class="landing-btn">
                        View all
                        <span class="material-symbols-rounded" style="font-size: 18px;">arrow_forward</span>
                    </a>
                </div>

                <div class="art-grid">
                    @forelse ($featuredArtworks as $artwork)
                        <article class="art-card">
                            <a href="{{ route('artworks.show', $artwork) }}" class="art-card__image">
                                @if ($artwork->imageUrl())
                                    <img src="{{ $artwork->imageUrl() }}" alt="{{ $artwork->title ?: 'Artwork by ' . $artwork->artist->name }}">
                                @else
                                    <div style="align-items: center; display: grid; height: 100%; justify-items: center; padding: 24px; text-align: center;">
                                        <span style="color: #6b7280; font-family: 'Playfair Display', serif; font-size: 26px;">{{ $artwork->title ?: 'Untitled' }}</span>
                                    </div>
                                @endif
                            </a>
                            <div class="art-card__body">
                                <div class="art-card__title-row">
                                    <h3>{{ $artwork->title ?: 'Untitled' }}</h3>
                                    <span class="art-card__heart material-symbols-rounded" aria-hidden="true">favorite</span>
                                </div>
                                <p class="art-card__artist">Artist: {{ $artwork->artist->name }}</p>
                                <p class="art-card__meta">
                                    <span>Size: {{ $artwork->size?->label() ?: 'N/A' }}</span>
                                    <span>Technique: {{ $artwork->medium?->name ?: 'N/A' }}</span>
                                    <span>{{ $artwork->year ?: 'Year N/A' }}{{ $artwork->style?->name ? ' | ' . $artwork->style->name : '' }}</span>
                                </p>
                                <div class="art-card__foot">
                                    <span class="art-card__price">
                                        @if ($artwork->price && $artwork->displayPrice('BDT'))
                                            <small>Regular {{ 'BDT ' . number_format((float) $artwork->price, 0) }}</small>
                                        @endif
                                        {{ $artwork->displayPrice('BDT') ? 'BDT ' . number_format($artwork->displayPrice('BDT'), 0) : 'On request' }}
                                    </span>
                                    <a href="{{ route('artworks.show', $artwork) }}" class="art-card__link">Inquiry</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        @foreach ($images as $index => $image)
                            <article class="art-card">
                                <div class="art-card__image">
                                    <img src="{{ $image }}" alt="Sample artwork">
                                </div>
                                <div class="art-card__body">
                                    <div class="art-card__title-row">
                                        <h3>{{ $sampleTitles[$index % count($sampleTitles)] }}</h3>
                                        <span class="art-card__heart material-symbols-rounded" aria-hidden="true">favorite</span>
                                    </div>
                                    <p class="art-card__artist">Artist: ART Story</p>
                                    <p class="art-card__meta">
                                        <span>Size: N/A</span>
                                        <span>Technique: Mixed media</span>
                                    </p>
                                    <div class="art-card__foot">
                                        <span class="art-card__price">On request</span>
                                        <a href="{{ route('artworks.index') }}" class="art-card__link">Inquiry</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    @endforelse
                </div>
            </section>

            <section class="split-feature">
                <article>
                    <span class="landing-kicker">What we offer</span>
                    <h2 class="landing-title" style="margin-top: 18px;">{{ $offerSection['title'] ?? 'What We Offer' }}</h2>
                    <p class="landing-copy" style="margin-top: 22px;">{{ $offerSection['description'] ?? 'ART Story gathers artworks that make spaces feel more thoughtful, personal, and alive.' }}</p>
                </article>
                <article>
                    <span class="landing-kicker">For collectors and artists</span>
                    <h2 class="landing-title" style="font-size: clamp(30px, 3.2vw, 44px); margin-top: 18px;">Let us be part of yours.</h2>
                    <p class="landing-copy" style="margin-top: 22px;">We help artworks travel beyond the studio by connecting emerging artists with people who value original creative expression.</p>
                    <a href="{{ route('contact') }}" class="landing-btn" style="margin-top: 30px;">
                        Talk to ART Story
                        <span class="material-symbols-rounded" style="font-size: 18px;">arrow_forward</span>
                    </a>
                </article>
            </section>

            <section id="featured-artists" class="artists-showcase">
                <div>
                    <span class="landing-kicker">{{ $artistLabel['category'] ?? 'Artists' }}</span>
                    <h2 class="landing-title" style="margin-top: 14px;">{{ $artistLabel['description'] ?? 'Creative souls we support and showcase.' }}</h2>
                    <p class="landing-copy" style="margin-top: 20px;">Explore the artists behind the collection and follow the works shaping ART Story's catalogue.</p>
                    <a href="{{ $artistLabel['url'] ?? route('artists.index') }}" class="landing-btn" style="margin-top: 30px;">
                        {{ $artistLabel['title'] ?? 'Featured Artists' }}
                        <span class="material-symbols-rounded" style="font-size: 18px;">arrow_forward</span>
                    </a>
                </div>

                <div class="artist-list">
                    @forelse ($featuredArtists as $artist)
                        <a href="{{ route('artists.show', $artist) }}" class="artist-card">
                            @if ($artist->pictureUrl())
                                <img src="{{ $artist->pictureUrl() }}" alt="{{ $artist->name }}">
                            @else
                                <div class="artist-card__initial">A{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            @endif
                            <div>
                                <h3>{{ $artist->name }}</h3>
                                <p>{{ number_format($artist->artworks_count) }} artworks</p>
                            </div>
                            <span class="artist-card__arrow material-symbols-rounded">arrow_forward</span>
                        </a>
                    @empty
                        <div style="border: 1px dashed #d1d5db; color: #6b7280; grid-column: 1 / -1; padding: 40px; text-align: center;">
                            Add artists from the admin panel to show them here.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

    </main>
</x-artstory.layout>
