@props(['siteSettings', 'title' => null, 'active' => 'home', 'hideChrome' => false])
@php
    $siteTitle = $siteSettings?->site_title ?: config('app.name', 'STS Institute');
    $courseLinks = \App\Models\Course::published()->limit(6)->get();
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ? $title . ' - ' . $siteTitle : $siteTitle }}</title>
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
        .text-muted{color:#5a6b84}.text-flame{color:#f38020}.text-ink{color:#192335}.text-flame-deep{color:#d96a12}.text-flame-soft{color:#ffb066}
        .border-line{border-color:#e2e7ee}.border-ink{border-color:#192335}.border-flame{border-color:#f38020}
        .topbar{background:#192335;color:#fff;font-size:13px}
        .topbar-inner{min-height:42px;display:flex;align-items:center;justify-content:space-between;gap:24px}
        .topbar-links{display:flex;align-items:center;gap:22px;color:rgba(255,255,255,.82)}
        .topbar a{color:#fff}
        .grid-drift{pointer-events:none}
        .theme-icon{width:38px;height:38px;border-radius:999px;border:1px solid #e2e7ee;display:grid;place-items:center;color:#192335;background:#fff}
        .dropdown a{white-space:normal}
        [data-theme="dark"] .theme-icon{background:#192335;color:#fff;border-color:rgba(255,255,255,.2)}
        [data-theme="dark"] .nav-solid{background:#fff;color:#192335}
    </style>
</head>
<body data-theme="light">
@unless ($hideChrome)
<div class="topbar">
    <div class="shell topbar-inner">
        <div class="mono tracking-[.24em] uppercase text-flame-soft">Official IDP IELTS Test Venue <span class="tracking-normal text-white/65">- Narsingdi, Bangladesh</span></div>
        <div class="topbar-links">
            @if ($siteSettings?->contact_phone)<a href="tel:{{ $siteSettings->contact_phone }}">{{ $siteSettings->contact_phone }}</a>@endif
            @if ($siteSettings?->contact_email)<a href="mailto:{{ $siteSettings->contact_email }}">{{ $siteSettings->contact_email }}</a>@endif
        </div>
    </div>
</div>
<header class="nav nav-solid">
    <div class="shell nav-inner">
        <a href="{{ route('landing.home') }}">
            @if ($siteSettings?->adminLogoUrl())
                <img src="{{ $siteSettings->adminLogoUrl() }}" alt="{{ $siteTitle }}" class="brand-logo">
            @else
                <span class="text-[24px] font-display text-ink">STS Institute</span>
            @endif
        </a>
        <nav class="nav-list">
            <a class="navlink" @if($active === 'about') aria-current="page" @endif href="{{ route('frontend.about') }}">About</a>
            <div class="has-drop relative">
                <a class="navlink" @if($active === 'courses') aria-current="page" @endif href="{{ route('frontend.courses') }}">Courses</a>
                <div class="dropdown">
                    <a href="{{ route('frontend.courses') }}">All courses</a>
                    @foreach ($courseLinks as $course)
                        <a href="{{ route('frontend.course.show', $course) }}">{{ $course->title }}</a>
                    @endforeach
                </div>
            </div>
            <a class="navlink" @if($active === 'journey') aria-current="page" @endif href="{{ route('frontend.inspiring-journey') }}">Inspiring journey</a>
            <a class="navlink" @if($active === 'stories') aria-current="page" @endif href="{{ route('frontend.success-stories') }}">Success stories</a>
            <a class="navlink" @if($active === 'blogs') aria-current="page" @endif href="{{ route('frontend.blogs') }}">Blogs</a>
            <a class="navlink" @if($active === 'contact') aria-current="page" @endif href="{{ route('frontend.contact') }}">Contact</a>
        </nav>
        <div class="nav-actions">
            <button id="theme-toggle" class="theme-icon" type="button" aria-label="Switch theme">&#9789;</button>
            <a href="{{ route('frontend.contact') }}" class="btn btn-primary btn-sm">Admission</a>
        </div>
    </div>
</header>
@endunless
<a id="main" tabindex="-1"></a>

{{ $slot }}

@unless ($hideChrome)
<footer class="foot bg-ink text-white">
    <div class="shell py-14 grid gap-10 md:grid-cols-[1fr_.8fr_.8fr_1.2fr]">
        <div>
            <h2 class="text-[26px] text-white">{{ $siteTitle }}</h2>
            <p class="mt-4 text-white/60">Dynamic STS Institute website managed from the admin dashboard.</p>
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
                <a href="{{ route('frontend.contact') }}">Contact</a>
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
@endunless
<script>
document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const toggle = document.getElementById("theme-toggle");
    const applyTheme = theme => {
        body.dataset.theme = theme;
        if (toggle) toggle.innerHTML = theme === "dark" ? "&#9728;" : "&#9789;";
        localStorage.setItem("sts-theme", theme);
    };
    applyTheme(localStorage.getItem("sts-theme") || "light");
    toggle?.addEventListener("click", () => applyTheme(body.dataset.theme === "dark" ? "light" : "dark"));

    document.querySelectorAll("[data-rail]").forEach(r => {
        const n = parseInt(r.dataset.rail, 10) || 19;
        const hit = parseInt(r.dataset.hit, 10);
        let out = "";
        for (let i = 0; i < n; i++) out += `<i class="${i === hit ? "hit" : (i % 2 === 0 ? "maj" : "")}"></i>`;
        r.innerHTML = out;
    });

    const reveal = document.querySelectorAll("[data-reveal]");
    if ("IntersectionObserver" in window) {
        const io = new IntersectionObserver(entries => entries.forEach((entry, index) => {
            if (!entry.isIntersecting) return;
            setTimeout(() => entry.target.classList.add("in"), Math.min(index * 70, 350));
            io.unobserve(entry.target);
        }), { threshold: .12, rootMargin: "0px 0px -60px" });
        reveal.forEach(el => io.observe(el));
    } else {
        reveal.forEach(el => el.classList.add("in"));
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
});
</script>
</body>
</html>

