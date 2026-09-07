<x-frontend.layout :site-settings="$siteSettings" title="Inspiring Journey" active="journey">
    <section class="bg-ink text-white pt-16 pb-20 md:pt-24 md:pb-28 relative overflow-hidden">
        <div class="grid-drift opacity-[.14]"></div>
        <div class="shell relative grid gap-12 lg:grid-cols-[1.3fr_1fr] lg:gap-16 items-end">
            <div><span class="eyebrow on-dark">Inspiring journey</span><h1 class="mt-6 text-white text-[clamp(32px,5.2vw,54px)]">Milestones from the STS journey.</h1><p class="mt-6 text-white/75 text-[17px] leading-relaxed max-w-xl">Every stage is managed from the Inspiring Journey module.</p></div>
            <div class="rounded-2xl border border-white/12 bg-white/[.05] p-7"><p class="mono text-[10px] tracking-[.18em] uppercase text-white/45">The distance travelled</p><div class="mt-7 bandrail text-white/35" data-rail="19" data-hit="15"></div></div>
        </div>
    </section>
    <div class="shell py-16 md:py-24 grid gap-12 lg:grid-cols-[220px_1fr] lg:gap-20 items-start max-w-[1060px]">
        <aside class="hidden lg:block sticky top-28"><p class="mono text-[10px] tracking-[.18em] uppercase text-muted">Where we are</p><div class="mt-4 font-display text-[54px] text-flame leading-none">{{ $journeyRecords->last()?->year }}</div><p class="mono text-[11px] tracking-[.14em] uppercase text-muted mt-2">Latest milestone</p></aside>
        <article class="prose min-w-0">
            @foreach ($journeyRecords as $item)
                <section><h2>{{ $item->year }} - {{ $item->title }}</h2><p>{{ $item->description }}</p></section>
            @endforeach
        </article>
    </div>
</x-frontend.layout>
