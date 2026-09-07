<x-frontend.layout :site-settings="$siteSettings" :title="$post->title" active="blogs">
    <section class="bg-ink text-white pt-16 pb-20 md:pt-24 md:pb-24 relative overflow-hidden">
        <div class="grid-drift opacity-[.14]"></div>
        <div class="shell relative"><nav class="text-[13px] text-white/50 flex items-center gap-2 flex-wrap"><a href="{{ route('landing.home') }}" class="hover:text-white">Home</a><span>/</span><a href="{{ route('frontend.blogs') }}" class="hover:text-white">Blogs</a></nav><span class="eyebrow on-dark mt-8">{{ $post->category }}</span><h1 class="mt-6 text-white text-[clamp(34px,5.6vw,56px)] max-w-3xl">{{ $post->title }}</h1><p class="mt-6 text-white/75 text-[17px] leading-relaxed max-w-2xl">{{ $post->excerpt }}</p></div>
    </section>
    <section class="section bg-white"><div class="shell max-w-[920px]"><img src="{{ $post->imageUrl() ?: asset('theme/sts-website/assets/img/glance-2.jpg') }}" alt="{{ $post->title }}" class="rounded-2xl w-full mb-10"><article class="prose max-w-none">{!! $post->content ?: nl2br(e($post->excerpt)) !!}</article></div></section>
</x-frontend.layout>
