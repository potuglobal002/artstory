<x-frontend.layout :site-settings="$siteSettings" title="Success Stories" active="stories">
    <section class="bg-ink text-white pt-16 pb-20 md:pt-24 md:pb-24 relative overflow-hidden">
        <div class="grid-drift opacity-[.14]"></div>
        <div class="shell relative">
            <span class="eyebrow on-dark">Success stories</span>
            <h1 class="mt-6 text-white text-[clamp(34px,5.6vw,56px)] max-w-3xl">Every story starts with a clear first step.</h1>
            <p class="mt-6 text-white/75 text-[17px] leading-relaxed max-w-2xl">Scores, destinations and honest words from STS learners.</p>
        </div>
    </section>
    <section class="section bg-white"><div class="shell"><div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($storyRecords as $story)
            <article class="card p-6 flex flex-col" data-reveal><div class="flex items-center gap-4"><div class="w-12 h-12 rounded-full bg-ember grid place-items-center font-display text-flame-deep text-lg shrink-0">{{ mb_substr($story->name, 0, 1) }}</div><div class="min-w-0"><h3 class="text-[17px] leading-tight truncate">{{ $story->name }}</h3><p class="text-[13px] text-muted truncate">{{ $story->course }} · {{ $story->meta }}</p></div><div class="ml-auto text-right shrink-0"><div class="font-display text-[26px] text-flame leading-none">{{ $story->result }}</div><div class="mono text-[10px] tracking-[.14em] uppercase text-muted mt-1">Result</div></div></div><p class="mt-5 text-[14.5px] leading-relaxed text-[#2c3a52] flex-1">"{{ $story->quote }}"</p></article>
        @endforeach
    </div></div></section>
</x-frontend.layout>
