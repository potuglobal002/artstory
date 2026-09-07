<x-frontend.layout :site-settings="$siteSettings" title="Blogs" active="blogs">
    <section class="bg-ink text-white pt-16 pb-20 md:pt-24 md:pb-24 relative overflow-hidden">
        <div class="grid-drift opacity-[.14]"></div>
        <div class="shell relative"><span class="eyebrow on-dark">From the blog</span><h1 class="mt-6 text-white text-[clamp(34px,5.6vw,56px)] max-w-3xl">Guidance we would give you across the desk.</h1><p class="mt-6 text-white/75 text-[17px] leading-relaxed max-w-2xl">Articles, guides and updates from STS departments.</p></div>
    </section>
    <section class="section bg-white"><div class="shell"><div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($blogRecords as $post)
            <a href="{{ route('frontend.blog-details', $post) }}" class="card overflow-hidden flex flex-col group" data-reveal><div class="ph h-44" data-label="{{ $post->category }}"><img src="{{ $post->imageUrl() ?: asset('theme/sts-website/assets/img/glance-2.jpg') }}" alt="{{ $post->title }}" loading="lazy"></div><div class="p-6 flex flex-col flex-1"><div class="flex items-center gap-3 mono text-[11px] tracking-[.14em] uppercase text-muted"><span class="text-flame">{{ $post->category }}</span><span>|</span><span>{{ optional($post->published_at)->format('M d, Y') ?: 'Latest' }}</span></div><h3 class="mt-3 text-[19px] leading-snug group-hover:text-flame-deep transition-colors">{{ $post->title }}</h3><p class="mt-2.5 text-[14.5px] text-muted leading-relaxed flex-1">{{ $post->excerpt }}</p></div></a>
        @endforeach
    </div></div></section>
</x-frontend.layout>
