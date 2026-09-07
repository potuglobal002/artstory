@php
    use App\Models\LandingPage;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $page = $landingPage ?: (new LandingPage())->forceFill(LandingPage::defaults());
    $siteTitle = $siteSettings?->site_title ?: config('app.name', 'STS Institute');
    $metaTitle = $siteSettings?->meta_title ?: 'STS Institute - Secret to Success';
    $metaDescription = $siteSettings?->meta_description ?: $page->hero_subtitle;
    $assetBase = asset('theme/sts-website/assets');
    $courseFallbacks = [
        asset('theme/sts-website/assets/img/course-ielts-academic.svg'),
        asset('theme/sts-website/assets/img/course-pte-academic.svg'),
        asset('theme/sts-website/assets/img/course-ielts-crash.svg'),
        asset('theme/sts-website/assets/img/course-junior-english.svg'),
    ];
    $glanceFallbacks = [
        asset('theme/sts-website/assets/img/glance-1.jpg'),
        asset('theme/sts-website/assets/img/glance-2.jpg'),
        asset('theme/sts-website/assets/img/glance-3.jpg'),
        asset('theme/sts-website/assets/img/glance-1.jpg'),
    ];
    $imageUrl = fn (?string $path, ?string $fallback = null): ?string => $path
        ? (str_starts_with($path, 'landing-assets/') ? asset($path) : Storage::disk('public')->url($path))
        : $fallback;
    $courseCards = isset($courses) && $courses->isNotEmpty()
        ? $courses
        : collect($page->programs ?: [])->map(fn ($program) => (object) [
            'title' => $program['title'] ?? 'Program',
            'slug' => Str::slug($program['title'] ?? 'program'),
            'category' => $program['category'] ?? 'Program',
            'badge' => null,
            'excerpt' => $program['description'] ?? '',
            'duration' => $program['duration'] ?? null,
            'sessions' => null,
            'fee' => null,
            'target_score' => null,
            'image_path' => $program['image_path'] ?? null,
            'imageUrl' => fn () => null,
        ]);
    $journeyCards = isset($journeyRecords) && $journeyRecords->isNotEmpty()
        ? $journeyRecords
        : collect($page->journey_items ?: [])->map(fn ($item, $index) => (object) [
            'year' => $item['year'] ?? '',
            'title' => $item['title'] ?? '',
            'description' => $item['description'] ?? '',
            'image_path' => $item['image_path'] ?? null,
            'sort_order' => $index,
        ]);
    $storyCards = isset($storyRecords) && $storyRecords->isNotEmpty()
        ? $storyRecords
        : collect($page->success_stories ?: [])->map(fn ($story, $index) => (object) [
            'name' => $story['name'] ?? '',
            'course' => $story['meta'] ?? '',
            'result' => $story['result'] ?? '',
            'meta' => $story['meta'] ?? '',
            'quote' => $story['quote'] ?? '',
            'image_path' => $story['image_path'] ?? null,
            'sort_order' => $index,
        ]);
    $blogCards = isset($blogRecords) && $blogRecords->isNotEmpty()
        ? $blogRecords
        : collect($page->blog_posts ?: [])->map(fn ($post, $index) => (object) [
            'title' => $post['title'] ?? '',
            'slug' => Str::slug($post['title'] ?? 'blog'),
            'category' => 'Guide',
            'excerpt' => $post['excerpt'] ?? '',
            'published_at' => null,
            'image_path' => $post['image_path'] ?? null,
            'sort_order' => $index,
        ]);
    $services = collect($page->services ?: [])->values();
    $trustStats = collect($page->trust_stats ?: [])->values();
    $approachSteps = collect($page->approach_steps ?: [])->values();
    $videoUrl = $page->hero_video_url;

    if ($videoUrl && str_contains($videoUrl, 'watch?v=')) {
        $videoUrl = str_replace('watch?v=', 'embed/', $videoUrl);
    }
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $metaTitle }}</title>
    @if ($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if ($siteSettings?->meta_keywords)
        <meta name="keywords" content="{{ $siteSettings->meta_keywords }}">
    @endif
    @if ($siteSettings?->meta_robots)
        <meta name="robots" content="{{ $siteSettings->meta_robots }}">
    @endif
    @if ($siteSettings?->canonical_url)
        <link rel="canonical" href="{{ $siteSettings->canonical_url }}">
    @endif
    @if ($siteSettings?->faviconUrl())
        <link rel="icon" href="{{ $siteSettings->faviconUrl() }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700;12..96,800&family=IBM+Plex+Mono:wght@400;500;600&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme/sts-website/assets/app.css') }}">
    <style>
        .bg-ink{background:#192335}.bg-mist{background:#f4f6f8}.bg-ember{background:#fff3e8}.bg-white{background:#fff}
        .text-muted{color:#5a6b84}.text-flame{color:#f38020}.text-ink{color:#192335}
        .border-line{border-color:var(--line)}.border-flame{border-color:var(--flame)}
        .theme-icon{width:38px;height:38px;border-radius:999px;border:1px solid rgba(255,255,255,.28);display:grid;place-items:center;color:#fff;background:rgba(255,255,255,.08);backdrop-filter:blur(8px)}
        .landing-header{position:absolute!important;top:0!important;left:0!important;right:0!important;z-index:50!important}
        .landing-header .topbar{background:rgba(25,35,53,.78);color:#c8d2e2;font-size:12.5px}
        .landing-header .topbar-inner{min-height:34px;padding-block:7px;display:flex;align-items:center;justify-content:space-between;gap:18px}
        .landing-header .topbar-links{display:flex;align-items:center;gap:18px;flex-shrink:0}
        .landing-header .topbar a:hover{color:#fff}
        .grid-drift{pointer-events:none}
        [data-theme="dark"] .theme-icon{background:rgba(25,35,53,.9)}
        [data-theme="dark"] .stage-scrim{background:linear-gradient(90deg,rgba(3,9,18,.72) 0%,rgba(3,9,18,.45) 42%,rgba(3,9,18,.18) 70%,rgba(3,9,18,.55) 100%),linear-gradient(180deg,rgba(15,22,36,.45) 0%,rgba(15,22,36,.2) 36%,rgba(15,22,36,.68) 100%)}
    </style>
</head>
<body data-theme="light">
@if (! $page->is_active)
    <main class="min-h-screen grid place-items-center shell text-center">
        <div>
            <span class="eyebrow">STS Institute</span>
            <h1 class="h-sec mt-5">Landing page is currently unpublished.</h1>
        </div>
    </main>
@else
<div class="relative">
    <section class="stage">
        <div class="filmstrip" aria-hidden="true">
            @foreach ($glanceFallbacks as $index => $image)
                <div class="layer" style="background:url('{{ $image }}') center/cover, radial-gradient(90% 120% at 20% 20%, #2a3a5c, #101827)"></div>
            @endforeach
            <div class="grid-drift"></div>
        </div>
        <div class="stage-scrim"></div>
        <div class="hero-video" aria-label="STS Institute hero video">
            <video autoplay muted loop playsinline preload="metadata" aria-hidden="true">
                <source src="{{ asset('theme/sts-website/assets/electricity.mp4') }}" type="video/mp4">
            </video>
        </div>

        <div class="hero-content">
            <div class="shell">
                <div class="hero-copy">
                    <span class="eyebrow on-dark">{{ $page->hero_eyebrow ?: 'STS at a glance' }}</span>
                    <h1 class="mt-6 text-white text-[clamp(18px,6.4vw,35px)]">
                        {{ $page->hero_title ?: 'The secret is not a shortcut.' }}<br>
                        It is <span class="ticker-word" id="ticker">standing by your goal</span>.
                    </h1>
                    @if ($page->hero_subtitle)
                        <p class="mt-6 text-white/80 text-[16.5px] md:text-[18px] leading-relaxed max-w-xl">{{ $page->hero_subtitle }}</p>
                    @endif
                    <div class="mt-9 flex flex-wrap items-center gap-3">
                        <a href="{{ $page->hero_cta_url ?: route('frontend.courses') }}" class="btn btn-primary">{{ $page->hero_cta_label ?: 'Get admission' }}</a>
                        <a href="{{ route('frontend.courses') }}" class="btn btn-glass">Browse all courses</a>
                    </div>
                </div>

                <div class="mt-12 md:mt-16 pt-7 border-t border-white/15 grid gap-7 md:grid-cols-[auto_1fr] md:items-end">
                    <div class="bandrail text-white/60" data-rail="19" data-hit="15"></div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 md:justify-items-end md:text-right">
                        @foreach ($trustStats->take(4) as $stat)
                            <div>
                                <div class="font-display text-white text-[26px] leading-none">{{ $stat['value'] ?? '' }}</div>
                                <div class="mono text-[10px] tracking-[.16em] uppercase text-white/50 mt-1.5">{{ $stat['label'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <header id="site-header" data-mode="overlay" data-active="home" class="landing-header">
        <a href="#main" class="skip">Skip to content</a>
        <div class="topbar relative z-40">
            <div class="shell topbar-inner">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="inline-flex items-center gap-1.5 shrink-0 text-flame-soft mono text-[10px] tracking-[.18em] uppercase">
                        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6.5 8.6l1.4 1.4 3.3-3.4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 1.6l5.2 2v4.1c0 3.2-2.1 5.4-5.2 6.7-3.1-1.3-5.2-3.5-5.2-6.7V3.6L8 1.6z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
                        Official IDP IELTS test venue
                    </span>
                    <span class="hidden md:inline text-[12.5px] truncate opacity-80">- Narsingdi, Bangladesh</span>
                </div>
                <div class="topbar-links">
                    @if ($siteSettings?->contact_phone)<a href="tel:{{ $siteSettings->contact_phone }}">{{ $siteSettings->contact_phone }}</a>@endif
                    @if ($siteSettings?->contact_email)<a href="mailto:{{ $siteSettings->contact_email }}">{{ $siteSettings->contact_email }}</a>@endif
                    @if ($siteSettings?->facebook_url)
                        <a href="{{ $siteSettings->facebook_url }}" target="_blank" rel="noopener" aria-label="STS on Facebook">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.25-1.5 1.55-1.5H16.7V3.6c-.29-.04-1.28-.13-2.43-.13-2.4 0-4.05 1.47-4.05 4.17V9.9H7.5V13h2.72v8h3.28z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <nav class="nav nav-over" id="mainnav">
        <div class="shell nav-inner">
                <a href="{{ route('landing.home') }}" class="flex items-center gap-3 shrink-0" aria-label="STS Institute - home">
                    @if ($siteSettings?->adminLogoUrl())
                        <img src="{{ $siteSettings->adminLogoUrl() }}" alt="{{ $siteTitle }}" class="brand-logo brightness-0 invert" id="brandmark">
                    @else
                        <span class="text-white text-[22px] font-display">STS Institute</span>
                    @endif
                </a>
            <ul class="nav-list">
                <li><a class="navlink" aria-current="page" href="{{ route('landing.home') }}">Home</a></li>
                <li><a class="navlink" href="{{ route('frontend.about') }}">About</a></li>
                <li class="has-drop relative">
                    <a class="navlink" href="{{ route('frontend.courses') }}">Courses <svg width="10" height="10" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4.5L6 8.5L10 4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></a>
                    <div class="dropdown">
                        <a href="{{ route('frontend.courses') }}">All courses</a>
                        @foreach ($courseCards->take(6) as $course)
                            <a href="{{ isset($course->slug) ? route('frontend.course.show', $course->slug) : route('frontend.courses') }}">{{ $course->title }}</a>
                        @endforeach
                    </div>
                </li>
                <li class="has-drop relative">
                    <a class="navlink" href="{{ route('frontend.success-stories') }}">Stories <svg width="10" height="10" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4.5L6 8.5L10 4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></a>
                    <div class="dropdown">
                        <a href="{{ route('frontend.success-stories') }}">Success stories</a>
                        <a href="{{ route('frontend.inspiring-journey') }}">Inspiring journey</a>
                    </div>
                </li>
                <li><a class="navlink" href="{{ route('frontend.blogs') }}">Blog</a></li>
                <li><a class="navlink" href="{{ route('frontend.contact') }}">Contact</a></li>
            </ul>
            <div class="nav-actions">
                <a href="{{ route('frontend.courses') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">Get admission</a>
            </div>
        </div>
        </nav>
    </header>
    <a id="main" tabindex="-1"></a>
</div>

<div class="bg-ink text-white/55">
    <div class="shell py-4 marquee-mask overflow-hidden">
        <div class="marquee mono text-[11.5px] tracking-[.2em] uppercase">
            @foreach ($services->concat($services) as $service)
                <span>{{ $service['title'] ?? 'STS Institute' }}</span>
            @endforeach
        </div>
    </div>
</div>

<section class="section bg-white">
    <div class="shell">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <span class="eyebrow">Our journey</span>
                <h2 class="h-sec mt-5 max-w-xl">From one classroom to a complete student success path</h2>
            </div>
            <p class="lede max-w-md">Every stop here is managed from the Inspiring Journey admin module.</p>
        </div>

        <div class="mt-14 rail" id="journey">
            <div class="rail-fill" id="rail-fill"></div>
            <div class="flex gap-8 md:gap-6 overflow-x-auto noscroll pb-4 snap-x snap-mandatory">
                @foreach ($journeyCards as $item)
                    <div class="stop snap-start shrink-0 w-[240px] md:w-[260px]">
                        <div class="h-[38px] flex items-end"><span class="mono text-[12px] tracking-[.16em] text-flame">{{ $item->year }}</span></div>
                        <div class="h-[14px] flex items-center"><span class="stop-dot"></span></div>
                        <h3 class="mt-5 text-[18px] leading-snug pr-4">{{ $item->title }}</h3>
                        <p class="mt-2.5 text-[14px] text-muted leading-relaxed pr-4">{{ $item->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <p class="mono text-[11px] tracking-[.16em] uppercase text-muted mt-2 lg:hidden">Swipe the timeline &rarr;</p>
    </div>
</section>

<section class="section bg-mist" id="services">
    <div class="shell">
        <span class="eyebrow">Our services</span>
        <div class="mt-5 flex flex-wrap items-end justify-between gap-6">
            <h2 class="h-sec max-w-xl">One stop, from your first class to your result day</h2>
            <a href="#contact" class="btn btn-ghost btn-sm">Talk to a counsellor</a>
        </div>

        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $index => $service)
                @php $serviceImage = $imageUrl($service['image_path'] ?? null, $courseFallbacks[$index % count($courseFallbacks)]); @endphp
                <div class="card p-7" data-reveal>
                    <div class="card-media">
                        <img src="{{ $serviceImage }}" alt="{{ $service['title'] ?? 'Service' }}" class="w-11 h-11 rounded-xl object-cover">
                    </div>
                    <h3 class="mt-5 text-[20px]">{{ $service['title'] ?? '' }}</h3>
                    <p class="mt-2.5 text-[14.5px] text-muted leading-relaxed">{{ $service['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section bg-white" id="programs">
    <div class="shell">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <span class="eyebrow">Popular programs</span>
                <h2 class="h-sec mt-5 max-w-lg">What most students start with</h2>
            </div>
            <a href="{{ route('frontend.courses') }}" class="btn btn-ghost btn-sm">All courses</a>
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($courseCards->take(8) as $index => $course)
                @php
                    $courseImage = method_exists($course, 'imageUrl') ? ($course->imageUrl() ?: $courseFallbacks[$index % count($courseFallbacks)]) : $courseFallbacks[$index % count($courseFallbacks)];
                    $courseUrl = isset($course->slug) ? route('frontend.course.show', $course->slug) : route('frontend.courses');
                @endphp
                <a href="{{ $courseUrl }}" class="card p-6 flex flex-col group" data-reveal>
                    <div class="card-thumb">
                        <img src="{{ $courseImage }}" alt="{{ $course->title }}" loading="lazy">
                    </div>
                    <div class="mt-4">
                        <div class="flex items-start justify-between gap-3">
                            <span class="mono text-[11px] tracking-[.16em] uppercase text-muted">{{ $course->category ?? 'Course' }}</span>
                            @if ($course->badge ?? false)
                                <span class="mono text-[10px] tracking-[.14em] uppercase px-2 py-1 rounded-md bg-ink text-white">{{ $course->badge }}</span>
                            @endif
                        </div>
                        <h3 class="mt-3 text-[21px] leading-tight">{{ $course->title }}</h3>
                    </div>
                    <p class="mt-2.5 text-[14.5px] text-muted leading-relaxed flex-1">{{ $course->excerpt ?? '' }}</p>
                    <div class="mt-5 flex flex-wrap items-center gap-2">
                        @if ($course->target_score ?? false)
                            <span class="band-chip">{{ $course->target_score }}</span>
                        @endif
                        @if ($course->duration ?? false)
                            <span class="text-[13px] text-muted">{{ $course->duration }}</span>
                        @endif
                        @if ($course->sessions ?? false)
                            <span class="w-1 h-1 rounded-full bg-line"></span><span class="text-[13px] text-muted">{{ $course->sessions }}</span>
                        @endif
                    </div>
                    <div class="mt-5 pt-5 border-t border-line flex items-center justify-between">
                        <span class="text-[15px] font-semibold">{{ $course->fee ? 'BDT ' . $course->fee : 'Details' }}</span>
                        <span class="text-flame text-[14px] font-semibold">View course &rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section bg-ink text-white">
    <div class="shell">
        <div class="grid gap-14 lg:grid-cols-[1fr_1.05fr] lg:gap-20 items-start">
            <div>
                <span class="eyebrow on-dark">Why trust us</span>
                <h2 class="h-sec mt-5 text-white">Being a training provider is one thing. Being trusted by students is another.</h2>
                <p class="mt-6 text-white/70 text-[16px] leading-relaxed">This section uses the theme infographic format, now populated from the landing trust statistics.</p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <a href="{{ route('frontend.about') }}" class="btn btn-glass btn-sm">Read the full story</a>
                    <a href="{{ route('frontend.inspiring-journey') }}" class="btn btn-glass btn-sm">Inspiring journey</a>
                </div>
            </div>

            <div class="grid gap-8" data-reveal>
                <div class="grid grid-cols-3 gap-4">
                    @foreach ($trustStats->take(3) as $stat)
                        @php $count = preg_replace('/[^0-9.]/', '', (string) ($stat['value'] ?? '0')) ?: 0; @endphp
                        <div class="rounded-xl border border-white/12 bg-white/[.04] p-5">
                            <div class="font-display text-[34px] text-flame leading-none"><span data-count="{{ $count }}">0</span>{{ preg_replace('/[0-9.]/', '', (string) ($stat['value'] ?? '')) }}</div>
                            <div class="mono text-[10px] tracking-[.15em] uppercase text-white/55 mt-2">{{ $stat['label'] ?? '' }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="grid gap-6">
                    @foreach ($trustStats->take(4) as $index => $stat)
                        @php $w = [68, 84, 91, 100][$index] ?? 75; @endphp
                        <div>
                            <div class="flex items-baseline justify-between mb-2.5">
                                <span class="text-[14.5px] text-white/85">{{ $stat['description'] ?? $stat['label'] ?? '' }}</span>
                                <span class="mono text-[13px] text-flame">{{ $w }}%</span>
                            </div>
                            <div class="meter bg-white/12"><i data-w="{{ $w }}"></i></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-white">
    <div class="shell">
        <div class="max-w-2xl">
            <span class="eyebrow">Beginner to success</span>
            <h2 class="h-sec mt-5">Five steps, and we will tell you honestly which one you are on</h2>
            <p class="lede mt-5">This path is dynamic from Landing Page settings, using the original theme stair layout.</p>
        </div>

        <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-5 lg:items-end">
            @foreach ($approachSteps->take(5) as $index => $step)
                <div class="stair p-6 lg:mb-{{ min(32, $index * 8) }}" data-reveal @if($index === 4) style="border-color:#f38020" @endif>
                    <span class="num">STEP {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="mt-3 text-[19px]">{{ $step['title'] ?? '' }}</h3>
                    <p class="mt-2 text-[14px] text-muted leading-relaxed">{{ $step['description'] ?? '' }}</p>
                    <div class="mt-5 bandrail" data-rail="19" data-hit="{{ min(18, $index * 4) }}"></div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section bg-mist" id="stories">
    <div class="shell">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <span class="eyebrow">Success stories</span>
                <h2 class="h-sec mt-5 max-w-lg">Real scores, real students, real universities</h2>
            </div>
            <a href="{{ route('frontend.success-stories') }}" class="btn btn-ghost btn-sm">See all stories</a>
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($storyCards->take(6) as $story)
                <article class="card p-6 flex flex-col" data-reveal>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-ember grid place-items-center font-display text-flame text-lg shrink-0">{{ Str::substr($story->name, 0, 1) }}</div>
                        <div class="min-w-0">
                            <h3 class="text-[17px] leading-tight truncate">{{ $story->name }}</h3>
                            <p class="text-[13px] text-muted truncate">{{ $story->course ?? $story->meta ?? '' }}</p>
                        </div>
                        <div class="ml-auto text-right shrink-0">
                            <div class="font-display text-[26px] text-flame leading-none">{{ $story->result }}</div>
                            <div class="mono text-[10px] tracking-[.14em] uppercase text-muted mt-1">Result</div>
                        </div>
                    </div>
                    <p class="mt-5 text-[14.5px] leading-relaxed text-[#2c3a52] flex-1">"{{ $story->quote }}"</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section bg-white" id="blog">
    <div class="shell">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <span class="eyebrow">From the blog</span>
                <h2 class="h-sec mt-5 max-w-lg">Guidance we would give you across the desk</h2>
            </div>
            <a href="{{ route('frontend.blogs') }}" class="btn btn-ghost btn-sm">All articles</a>
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($blogCards->take(3) as $index => $post)
                @php $postImage = method_exists($post, 'imageUrl') ? ($post->imageUrl() ?: $glanceFallbacks[$index % count($glanceFallbacks)]) : $glanceFallbacks[$index % count($glanceFallbacks)]; @endphp
                <a href="{{ route('frontend.blog-details', $post->slug) }}" class="card overflow-hidden flex flex-col group" data-reveal>
                    <div class="ph h-44" data-label="{{ $post->category ?? 'Blog' }}"><img src="{{ $postImage }}" alt="{{ $post->title }}" loading="lazy"></div>
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center gap-3 mono text-[11px] tracking-[.14em] uppercase text-muted">
                            <span class="text-flame">{{ $post->category ?? 'Guide' }}</span><span>|</span><span>{{ optional($post->published_at)->format('M d, Y') ?: 'Latest' }}</span>
                        </div>
                        <h3 class="mt-3 text-[19px] leading-snug group-hover:text-flame transition-colors">{{ $post->title }}</h3>
                        <p class="mt-2.5 text-[14.5px] text-muted leading-relaxed flex-1">{{ $post->excerpt }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-ember">
    <div class="shell py-16 md:py-20 grid gap-8 md:grid-cols-[1.3fr_auto] md:items-center">
        <div>
            <h2 class="text-[clamp(26px,3.6vw,38px)] max-w-2xl">{{ $page->footer_heading ?: 'Start where you actually are.' }}</h2>
            <p class="mt-4 text-muted text-[16px] max-w-xl">{{ $page->footer_note ?: 'Book a placement assessment or talk to our counselling team.' }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('frontend.courses') }}" class="btn btn-primary">Get admission</a>
            @if ($siteSettings?->contact_phone)
                <a href="tel:{{ $siteSettings->contact_phone }}" class="btn btn-ghost">Call us</a>
            @endif
        </div>
    </div>
</section>

<footer class="foot bg-ink text-white" id="contact">
    <div class="shell py-14 grid gap-10 md:grid-cols-[1fr_.8fr_.8fr_1.2fr]">
        <div>
            <h2 class="text-[26px] text-white">{{ $siteTitle }}</h2>
            <p class="mt-4 text-white/60">Download, visit, call or follow STS Institute.</p>
        </div>
        <div>
            <h3 class="text-white text-[18px]">Company</h3>
            <div class="mt-4 grid gap-3">
                <a href="{{ route('frontend.about') }}">About</a>
                <a href="{{ route('frontend.inspiring-journey') }}">Inspiring journey</a>
                <a href="{{ route('frontend.success-stories') }}">Success stories</a>
            </div>
        </div>
        <div>
            <h3 class="text-white text-[18px]">Others</h3>
            <div class="mt-4 grid gap-3">
                <a href="{{ route('frontend.courses') }}">Courses</a>
                <a href="{{ route('frontend.blogs') }}">Blogs</a>
            </div>
        </div>
        <div>
            <h3 class="text-white text-[18px]">Keep up with us</h3>
            <div class="mt-4 grid gap-3 text-white/65">
                @if ($siteSettings?->contact_phone)<p>Call us: <a class="text-flame" href="tel:{{ $siteSettings->contact_phone }}">{{ $siteSettings->contact_phone }}</a></p>@endif
                @if ($siteSettings?->contact_email)<p>Email: <a class="text-flame" href="mailto:{{ $siteSettings->contact_email }}">{{ $siteSettings->contact_email }}</a></p>@endif
                @if ($siteSettings?->address)<p>{{ $siteSettings->address }}</p>@endif
            </div>
        </div>
    </div>
    <div class="shell border-t border-white/10 py-6 text-center text-white/50 text-sm">{{ now()->year }} Copyright &copy; {{ $siteTitle }}. All rights reserved.</div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const themeToggle = document.getElementById("theme-toggle");
    const applyTheme = theme => {
        body.dataset.theme = theme;
        if (themeToggle) themeToggle.innerHTML = theme === "dark" ? "&#9728;" : "&#9789;";
        localStorage.setItem("sts-theme", theme);
    };
    applyTheme(localStorage.getItem("sts-theme") || "light");
    themeToggle?.addEventListener("click", () => applyTheme(body.dataset.theme === "dark" ? "light" : "dark"));

    document.querySelectorAll("[data-rail]").forEach(r => {
        const n = parseInt(r.dataset.rail, 10) || 19;
        const hit = parseInt(r.dataset.hit, 10);
        let out = "";
        for (let i = 0; i < n; i++) out += `<i class="${i === hit ? "hit" : (i % 2 === 0 ? "maj" : "")}"></i>`;
        r.innerHTML = out;
    });

    const reveal = document.querySelectorAll("[data-reveal]");
    if ("IntersectionObserver" in window) {
        const revealIo = new IntersectionObserver(entries => entries.forEach((entry, index) => {
            if (!entry.isIntersecting) return;
            setTimeout(() => entry.target.classList.add("in"), Math.min(index * 70, 350));
            revealIo.unobserve(entry.target);
        }), { threshold: .12, rootMargin: "0px 0px -60px" });
        reveal.forEach(el => revealIo.observe(el));
    } else {
        reveal.forEach(el => el.classList.add("in"));
    }

    const journey = document.getElementById("journey");
    if (journey) {
        const journeyIo = new IntersectionObserver(entries => entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            document.getElementById("rail-fill").style.width = "100%";
            journey.querySelectorAll(".stop").forEach((stop, index) => setTimeout(() => stop.classList.add("is-on"), 140 * index));
            journeyIo.disconnect();
        }), { threshold: .25 });
        journeyIo.observe(journey);
    }

    const animateCount = el => {
        const target = parseFloat(el.dataset.count || "0");
        const decimals = (el.dataset.count.split(".")[1] || "").length;
        const start = performance.now();
        const step = now => {
            const p = Math.min((now - start) / 1400, 1);
            const eased = 1 - Math.pow(1 - p, 3);
            el.textContent = (target * eased).toFixed(decimals);
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };
    const motionIo = new IntersectionObserver(entries => entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        if (entry.target.dataset.count) animateCount(entry.target);
        if (entry.target.dataset.w) entry.target.style.width = `${entry.target.dataset.w}%`;
        motionIo.unobserve(entry.target);
    }), { threshold: .45 });
    document.querySelectorAll("[data-count], .meter i").forEach(el => motionIo.observe(el));

    const words = ["standing by your goal", "refusing the shortcut", "staying passionate", "not giving up"];
    const ticker = document.getElementById("ticker");
    let index = 0;
    if (ticker && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        setInterval(() => {
            index = (index + 1) % words.length;
            ticker.style.opacity = 0;
            ticker.style.transition = "opacity .35s";
            setTimeout(() => { ticker.textContent = words[index]; ticker.style.opacity = 1; }, 350);
        }, 3200);
    }
});
</script>
@endif
</body>
</html>
