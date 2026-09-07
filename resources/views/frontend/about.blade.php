<x-frontend.layout :site-settings="$siteSettings" title="About STS Institute" active="about">
    <section class="bg-ink text-white pt-16 pb-20 md:pt-24 md:pb-28 relative overflow-hidden">
        <div class="grid-drift opacity-[.14]"></div>
        <div class="shell relative">
            <span class="eyebrow on-dark">About us</span>
            <h1 class="mt-6 text-white text-[clamp(34px,5.6vw,58px)] max-w-3xl">STS stands for Secret to Success. The secret is the part nobody wants to hear.</h1>
            <p class="mt-6 text-white/75 text-[17px] leading-relaxed max-w-2xl">Stand by your goals. Stay passionate. Avoid shortcuts. And most importantly, do not give up.</p>
            <div class="mt-10 bandrail text-white/50" data-rail="19" data-hit="15"></div>
        </div>
    </section>
    <section class="section bg-white">
        <div class="shell grid gap-14 lg:grid-cols-[1.1fr_1fr] lg:gap-20 items-start">
            <div>
                <span class="eyebrow">Founded 2019</span>
                <h2 class="h-sec mt-5">Built so students would not need to leave their district for better guidance</h2>
                <div class="mt-6 grid gap-4 text-[16px] leading-relaxed text-[#2c3a52]">
                    <p>STS Institute gives students the skills they need to hold their own in a global environment. That starts with English, test preparation, counselling and honest placement guidance.</p>
                    <p>We do not sell shortcuts. If your English is not ready for IELTS, we say so at the placement assessment and put you on the right path first.</p>
                </div>
            </div>
            <div class="grid gap-5">
                @foreach ($trustStats as $stat)
                    <div class="rounded-2xl border border-line p-7 bg-mist" data-reveal>
                        <h3 class="text-[20px]">{{ $stat['label'] ?? '' }}</h3>
                        <p class="mt-3 text-[15px] text-muted leading-relaxed">{{ $stat['description'] ?? '' }}</p>
                        <div class="font-display text-[34px] text-flame mt-5">{{ $stat['value'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section class="section bg-mist">
        <div class="shell">
            <span class="eyebrow">Our milestones</span>
            <h2 class="h-sec mt-5 max-w-xl">The journey that shaped STS</h2>
            <div class="mt-12 grid gap-5 md:grid-cols-3">
                @foreach ($journeyRecords->take(6) as $item)
                    <article class="card-flat p-7" data-reveal>
                        <span class="mono text-[12px] tracking-[.16em] text-flame">{{ $item->year }}</span>
                        <h3 class="mt-4 text-[20px]">{{ $item->title }}</h3>
                        <p class="mt-3 text-[15px] text-muted leading-relaxed">{{ $item->description }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</x-frontend.layout>
