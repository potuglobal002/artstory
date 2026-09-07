@props(['siteSettings' => null, 'title' => null, 'active' => 'home', 'immersiveHeader' => false, 'immersive' => false])
@php
    $siteTitle = $siteSettings?->site_title ?: 'ART Story';
    $siteLogo = $siteSettings?->loginLogoUrl() ?: $siteSettings?->adminLogoUrl() ?: $siteSettings?->defaultOgImageUrl();
    $description = 'Every art has a story that can unfold with infinite variety of perceptions and interpretations.';
    $artistUser = auth('artist')->user();
    $virtualGalleryEnabled = $siteSettings?->virtual_gallery_enabled ?? true;
    $artistLoginEnabled = $siteSettings?->artist_login_enabled ?? true;
    $exhibitionEnabled = $siteSettings?->exhibition_enabled ?? true;
    $eventsPrEnabled = $siteSettings?->events_pr_enabled ?? true;
    $navItems = [
        ['label' => 'Home', 'href' => route('home'), 'active' => 'home'],
        ['label' => 'About', 'href' => route('about'), 'active' => 'about'],
        ['label' => 'Artworks', 'href' => route('artworks.index'), 'active' => 'artworks'],
        ['label' => 'Artists', 'href' => route('artists.index'), 'active' => 'artists'],
        ...($exhibitionEnabled ? [['label' => 'Exhibition', 'href' => route('exhibitions.index'), 'active' => 'exhibitions']] : []),
        ...($eventsPrEnabled ? [['label' => 'Events & PR', 'href' => route('events.index'), 'active' => 'events']] : []),
        ['label' => 'Contact', 'href' => route('contact'), 'active' => 'contact'],
    ];
    if ($virtualGalleryEnabled) {
        $navItems[] = ['label' => 'Virtual Gallery', 'href' => route('virtual-galleries.index'), 'active' => 'virtual-galleries'];
    }
    $artistCtaLabel = $artistUser?->artistProfile ? 'Dashboard' : 'Artist Login';
    $artistCtaHref = $artistUser?->artistProfile ? route('artist.dashboard') : route('artist.login');
    $footerBrandText = $siteSettings?->footer_brand_text ?: 'Contemporary art for thoughtful spaces, connecting emerging artists with collectors around the globe.';
    $footerSectionsHeading = $siteSettings?->footer_sections_heading ?: 'Sections';
    $footerCollectorsHeading = $siteSettings?->footer_collectors_heading ?: 'Collectors';
    $footerContactHeading = $siteSettings?->footer_contact_heading ?: 'Contact';
    $footerCopyrightText = $siteSettings?->footer_copyright_text ?: 'All rights reserved.';
    $footerAddress = $siteSettings?->head_office_address ?: $siteSettings?->address ?: '8041 Commonwealth Blvd, Bellerose, NY 11426, USA';
    $footerSectionLinks = collect($siteSettings?->footer_section_links ?: [
        ['label' => 'Artworks', 'url' => route('artworks.index')],
        ['label' => 'Exhibition', 'url' => route('exhibitions.index')],
        ['label' => 'Events & PR', 'url' => route('events.index')],
        ['label' => 'About', 'url' => route('about')],
        ['label' => 'Artists', 'url' => route('artists.index')],
        ['label' => 'Contact', 'url' => route('contact')],
    ])->filter(fn ($link): bool => filled($link['label'] ?? null) && filled($link['url'] ?? null));
    $footerCollectorLinks = collect($siteSettings?->footer_collector_links ?: [
        ['label' => 'Artwork', 'url' => route('artworks.index')],
        ['label' => 'Delivery', 'url' => route('contact')],
        ['label' => 'Payment', 'url' => route('contact')],
        ['label' => 'Inquiry', 'url' => route('contact')],
        ['label' => 'Privacy Policy', 'url' => route('privacy-policy')],
        ['label' => 'Terms & Conditions', 'url' => route('terms-conditions')],
    ])->filter(fn ($link): bool => filled($link['label'] ?? null) && filled($link['url'] ?? null));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $siteSettings?->meta_description ?: $description }}">
        <title>{{ $title ? $title . ' - ' . $siteTitle : $siteTitle }}</title>
        @if ($siteSettings?->faviconUrl())
            <link rel="icon" href="{{ $siteSettings->faviconUrl() }}">
        @endif
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .site-header {
                position: sticky;
                top: 0;
                z-index: 60;
                border-bottom: 1px solid rgba(229, 231, 235, .9);
                background: rgba(255, 255, 255, .92);
                backdrop-filter: blur(18px);
                box-shadow: 0 10px 30px rgba(17, 24, 39, .05);
                transition: background .24s ease, border-color .24s ease, box-shadow .24s ease, backdrop-filter .24s ease;
            }

            .site-header--transparent {
                position: fixed;
                left: 0;
                right: 0;
                top: 0;
                border-bottom-color: transparent;
                background: transparent;
                backdrop-filter: none;
                box-shadow: none;
            }

            .site-header--transparent nav {
                text-shadow: 0 1px 16px rgba(255, 255, 255, .92);
            }

            .site-header--transparent .site-nav-link {
                color: #111827;
                font-weight: 900;
            }

            .site-header--transparent .site-nav-link:not(.is-active) {
                color: #374151;
            }

            .site-header--transparent .site-nav-icon {
                color: #111827;
                filter: drop-shadow(0 1px 10px rgba(255, 255, 255, .8));
            }

            .site-header__logo {
                height: 64px;
                max-width: 150px;
            }

            .site-header--transparent .site-header__logo {
                height: 68px;
                max-width: 160px;
            }

            .site-header--transparent.site-header--scrolled {
                border-bottom-color: rgba(229, 231, 235, .9);
                background: rgba(255, 255, 255, .94);
                backdrop-filter: blur(18px);
                box-shadow: 0 10px 30px rgba(17, 24, 39, .06);
            }

            .site-header--transparent.site-header--scrolled nav {
                text-shadow: none;
            }

            .site-header--transparent.site-header--scrolled .site-nav-icon {
                filter: none;
            }

            .site-footer {
                background: #0d0d0d;
                color: #fff;
                padding: 54px 0 34px;
            }

            .site-footer__grid {
                display: grid;
                gap: 34px;
                grid-template-columns: minmax(260px, 1.15fr) repeat(3, minmax(0, .75fr));
            }

            .site-footer__logo {
                filter: invert(1);
                height: 74px;
                margin-bottom: 20px;
                object-fit: contain;
                width: auto;
            }

            .site-footer__brand-text {
                color: rgba(255, 255, 255, .72);
                font-size: 15px;
                line-height: 1.8;
                max-width: 260px;
            }

            .site-footer h6 {
                color: rgba(255, 255, 255, .9);
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .18em;
                margin-bottom: 18px;
                text-transform: uppercase;
            }

            .site-footer a,
            .site-footer p {
                color: rgba(255, 255, 255, .66);
                font-size: 13px;
                line-height: 1.9;
            }

            .site-footer a:hover {
                color: #fff;
            }

            .site-footer__social {
                border-top: 1px solid rgba(255, 255, 255, .12);
                color: rgba(255, 255, 255, .68);
                display: flex;
                gap: 18px;
                margin-top: 20px;
                padding-top: 18px;
            }

            .site-footer__bottom {
                border-top: 1px solid rgba(255, 255, 255, .12);
                color: rgba(255, 255, 255, .45);
                font-size: 12px;
                margin-top: 38px;
                padding-top: 22px;
            }

            .art-pagination {
                align-items: center;
                border-top: 1px solid #e5e7eb;
                display: flex;
                gap: 24px;
                justify-content: space-between;
                padding-top: 30px;
            }

            .art-pagination__summary {
                color: #6b7280;
                font-size: 14px;
                line-height: 1.5;
            }

            .art-pagination__summary span {
                color: #111827;
                font-weight: 800;
            }

            .art-pagination__links {
                align-items: center;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                justify-content: flex-end;
            }

            .art-pagination__page,
            .art-pagination__control,
            .art-pagination__ellipsis {
                align-items: center;
                background: #fff;
                border: 1px solid #e5e7eb;
                color: #4b5563;
                display: inline-flex;
                font-size: 13px;
                font-weight: 800;
                height: 40px;
                justify-content: center;
                min-width: 40px;
                padding: 0 12px;
                transition: background .2s ease, border-color .2s ease, color .2s ease, transform .2s ease;
            }

            .art-pagination__page:hover,
            .art-pagination__control:hover {
                border-color: #111827;
                color: #111827;
                transform: translateY(-1px);
            }

            .art-pagination__page.is-active {
                background: #111827;
                border-color: #111827;
                color: #fff;
            }

            .art-pagination__control .material-symbols-rounded {
                font-size: 19px;
            }

            .art-pagination__control.is-disabled {
                background: #f9fafb;
                color: #c4c8d0;
                pointer-events: none;
            }

            .art-pagination__ellipsis {
                border-color: transparent;
                color: #9ca3af;
                min-width: 30px;
                padding: 0 4px;
            }

            .artwork-catalog-page.site-reveal,
            .artwork-catalog-page .site-reveal-child {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }

            .site-mobile-menu {
                background: rgba(255, 255, 255, .96);
                border-top: 1px solid rgba(17, 24, 39, .08);
                box-shadow: 0 18px 40px rgba(17, 24, 39, .08);
                color: #111827;
                padding: 10px 24px 18px;
            }

            .site-mobile-menu__top {
                display: none;
            }

            .site-mobile-menu__logo {
                max-height: 72px;
                max-width: 150px;
                object-fit: contain;
            }

            .site-mobile-menu__close {
                align-items: center;
                border: 1px solid rgba(255, 255, 255, .18);
                border-radius: 999px;
                color: #fff;
                display: grid;
                height: 42px;
                justify-items: center;
                width: 42px;
            }

            .site-mobile-menu__links {
                display: grid;
                gap: 0;
            }

            .site-mobile-menu__links a {
                border-bottom: 1px solid rgba(17, 24, 39, .08);
                color: #111827;
                font-size: 13px;
                font-weight: 900;
                letter-spacing: .12em;
                padding: 14px 0;
                text-transform: uppercase;
            }

            .site-header-cta {
                align-items: center;
                background: #111827;
                color: #fff;
                display: inline-flex;
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .12em;
                min-height: 38px;
                padding: 0 16px;
                text-transform: uppercase;
                transition: background .2s ease, color .2s ease, transform .2s ease;
                white-space: nowrap;
            }

            .site-header-cta:hover {
                background: #2f3b1f;
                transform: translateY(-1px);
            }

            body.site-menu-open {
                overflow: auto;
            }

            @media (max-width: 640px) {
                .site-header__logo {
                    height: 56px;
                    max-width: 135px;
                }

                .site-header--transparent {
                    background: rgba(255, 255, 255, .76);
                    backdrop-filter: blur(14px);
                    left: 0;
                    position: fixed;
                    right: 0;
                    top: 0;
                }

                .site-header--transparent img {
                    height: 64px !important;
                    max-width: 126px;
                }

                .site-header--transparent .site-nav-icon {
                    color: #030712;
                    filter: drop-shadow(0 1px 12px rgba(255, 255, 255, .95));
                }

                .site-reveal,
                .site-reveal-child,
                .site-parallax-soft {
                    opacity: 1 !important;
                    transform: none !important;
                    transition: none !important;
                }

                .site-footer {
                    padding: 38px 0 28px;
                }

                .site-footer__grid {
                    gap: 26px;
                    grid-template-columns: 1fr;
                }

                .site-footer__logo {
                    height: 80px;
                    margin-bottom: 16px;
                }

                .site-footer__brand-text {
                    max-width: 100%;
                }

                .site-footer h6 {
                    margin-bottom: 12px;
                }

                .site-footer ul {
                    display: grid;
                    gap: 10px 18px;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .site-footer li,
                .site-footer a {
                    line-height: 1.35;
                }

                .site-header-cta {
                    display: none;
                }

                .art-pagination {
                    align-items: flex-start;
                    flex-direction: column;
                    gap: 16px;
                    padding-top: 24px;
                }

                .art-pagination__links {
                    justify-content: flex-start;
                    width: 100%;
                }

                .art-pagination__page,
                .art-pagination__control,
                .art-pagination__ellipsis {
                    height: 38px;
                    min-width: 38px;
                }
            }

            .site-reveal {
                opacity: 0;
                transform: translateY(30px);
                transition: opacity .72s ease, transform .72s cubic-bezier(.2, .75, .2, 1);
            }

            .site-reveal.is-visible {
                opacity: 1;
                transform: translateY(0);
            }

            .site-reveal-child {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity .64s ease, transform .64s cubic-bezier(.2, .75, .2, 1);
                transition-delay: var(--reveal-delay, 0ms);
            }

            .is-visible .site-reveal-child {
                opacity: 1;
                transform: translateY(0);
            }

            .site-parallax-soft {
                transform: translate3d(0, var(--parallax-y, 0px), 0);
                transition: transform .12s linear;
                will-change: transform;
            }

            @media (prefers-reduced-motion: reduce) {
                .site-reveal,
                .site-reveal-child,
                .site-parallax-soft {
                    opacity: 1 !important;
                    transform: none !important;
                    transition: none !important;
                }
            }
        </style>
    </head>
    <body class="bg-[#0d0d0d] text-gray-900 antialiased">
        @unless ($immersive)
        <header @class([
            'site-header py-3',
            'site-header--transparent' => $immersiveHeader,
        ])>
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6" aria-label="Primary navigation">
                <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="{{ $siteTitle }} home">
                    @if ($siteLogo)
                        <img src="{{ $siteLogo }}" alt="{{ $siteTitle }}" class="site-header__logo h-12 w-auto object-contain">
                    @else
                        <span @class([
                            'font-display text-2xl font-bold tracking-widest text-gray-950',
                        ])>{{ $siteTitle }}</span>
                    @endif
                </a>

                <div class="hidden items-center gap-7 md:flex">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}" @class([
                            'site-nav-link border-b-2 pb-1 text-xs uppercase tracking-wider transition hover:text-gray-950',
                            'is-active border-gray-950 font-black text-gray-950' => $active === $item['active'],
                            'border-transparent font-semibold text-gray-600' => $active !== $item['active'],
                        ])>{{ $item['label'] }}</a>
                    @endforeach
                </div>

                <div class="flex items-center gap-2">
                    @if ($artistLoginEnabled)
                        <a href="{{ $artistCtaHref }}" class="site-header-cta">{{ $artistCtaLabel }}</a>
                    @endif
                    <button type="button" data-menu-toggle class="site-nav-icon grid size-10 place-items-center text-gray-700 md:hidden" aria-label="Open menu" aria-expanded="false">
                        <span class="material-symbols-rounded" aria-hidden="true">menu</span>
                    </button>
                </div>
            </nav>

            <div data-mobile-menu class="site-mobile-menu hidden md:hidden" aria-label="Mobile navigation">
                <div class="site-mobile-menu__links">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}" @class([
                            'opacity-100' => $active === $item['active'],
                            'opacity-70' => $active !== $item['active'],
                        ])>{{ $item['label'] }}</a>
                    @endforeach
                    @if ($artistLoginEnabled)
                        <a href="{{ $artistCtaHref }}" @class([
                            'opacity-100' => $active === 'artist_portal',
                            'opacity-70' => $active !== 'artist_portal',
                        ])>{{ $artistCtaLabel }}</a>
                    @endif
                </div>
            </div>
        </header>
        @endunless

        <main @class(['min-h-screen bg-white' => ! $immersive, 'min-h-screen' => $immersive])>
            {{ $slot }}
        </main>

        @unless ($immersive)
        <footer class="site-footer">
            <div class="site-footer__grid mx-auto max-w-7xl px-6">
                <div>
                    @if ($siteLogo)
                        <img src="{{ $siteLogo }}" alt="{{ $siteTitle }}" class="site-footer__logo">
                    @else
                        <div class="mb-4 font-display text-xl font-bold tracking-widest">{{ $siteTitle }}</div>
                    @endif
                    <p class="site-footer__brand-text">{{ $footerBrandText }}</p>
                </div>
                <div>
                    <h6>{{ $footerSectionsHeading }}</h6>
                    <ul class="space-y-2">
                        @foreach ($footerSectionLinks as $link)
                            <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h6>{{ $footerCollectorsHeading }}</h6>
                    <ul class="space-y-2">
                        @foreach ($footerCollectorLinks as $link)
                            <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h6>{{ $footerContactHeading }}</h6>
                    <p class="mb-2 whitespace-pre-line">{{ $footerAddress }}</p>
                    <p class="mb-2">{{ $siteSettings?->contact_phone ?: '+1 (347) 562-3532' }}</p>
                    <p class="mb-2">{{ $siteSettings?->contact_email ?: 'info@artstoryglobal.com' }}</p>
                    <div class="site-footer__social">
                        <span class="material-symbols-rounded cursor-pointer hover:text-white">alternate_email</span>
                        <span class="material-symbols-rounded cursor-pointer hover:text-white">call</span>
                        <span class="material-symbols-rounded cursor-pointer hover:text-white">location_on</span>
                    </div>
                </div>
            </div>
            <div class="site-footer__bottom mx-auto max-w-7xl px-6">
                © {{ now()->year }} {{ $siteTitle }}. {{ $footerCopyrightText }}
            </div>
        </footer>
        @endunless

        <script>
            (() => {
                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const header = document.querySelector('.site-header--transparent');
                const sectionSet = new Set(document.querySelectorAll('main section:not(.art-cover-hero)'));

                if (header) {
                    const updateHeader = () => {
                        header.classList.toggle('site-header--scrolled', window.scrollY > 24);
                    };

                    window.addEventListener('scroll', updateHeader, { passive: true });
                    updateHeader();
                }

                document.querySelectorAll('body > main > main').forEach((pageMain) => {
                    if (! pageMain.querySelector('section')) {
                        sectionSet.add(pageMain);
                    }
                });

                const sections = Array.from(sectionSet);

                sections.forEach((section) => {
                    section.classList.add('site-reveal');

                    section
                        .querySelectorAll(':scope > *, .art-card, .artist-card, .story-point, .exhibition-preview__copy, .exhibition-preview__media, .split-feature article, .product-image, .product-info > *, .inquiry-band > *, .filter-rail, .catalog-title-row, .catalog-grid > *, .contact-card')
                        .forEach((child, index) => {
                            child.classList.add('site-reveal-child');
                            child.style.setProperty('--reveal-delay', `${Math.min(index * 55, 385)}ms`);
                        });
                });

                if (reduceMotion) {
                    sections.forEach((section) => section.classList.add('is-visible'));
                    return;
                }

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { rootMargin: '0px 0px -12% 0px', threshold: 0.14 });

                sections.forEach((section) => observer.observe(section));

                const parallaxTargets = document.querySelectorAll('.art-cover-hero, .story-intro__image, .exhibition-preview__media, .product-image');
                let ticking = false;

                const updateParallax = () => {
                    parallaxTargets.forEach((target) => {
                        const rect = target.getBoundingClientRect();
                        const progress = Math.max(-1, Math.min(1, (window.innerHeight / 2 - rect.top) / Math.max(rect.height, 1) - 0.5));

                        if (target.classList.contains('art-cover-hero')) {
                            target.style.setProperty('--hero-parallax-y', `${progress * 24}px`);
                            target.querySelector('.cover-hero__copy')?.style.setProperty('--parallax-y', `${progress * -10}px`);
                            target.querySelector('.cover-hero__copy')?.classList.add('site-parallax-soft');
                        } else {
                            target.style.setProperty('--parallax-y', `${progress * -12}px`);
                            target.classList.add('site-parallax-soft');
                        }
                    });

                    ticking = false;
                };

                window.addEventListener('scroll', () => {
                    if (! ticking) {
                        window.requestAnimationFrame(updateParallax);
                        ticking = true;
                    }
                }, { passive: true });

                updateParallax();
            })();
        </script>
    </body>
</html>
