@php
    $heroImage = $siteSettings?->imageUrl($siteSettings?->about_image_path) ?: asset('images/art-story-collage.jpg');
@endphp

<x-artstory.layout :site-settings="$siteSettings" title="About Us" active="about">
    <main>
        <section class="bg-white py-20">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-gray-500">{{ $siteSettings?->about_eyebrow ?: 'About us' }}</p>
                    <h1 class="mt-4 font-display text-5xl font-bold leading-tight text-black">{{ $siteSettings?->about_title ?: 'Art Story helps emerging artists move forward.' }}</h1>
                </div>
                <div class="text-lg leading-8 text-gray-700">
                    <p>{{ $siteSettings?->about_description ?: 'Art Story is a global platform focused on helping emerging artists move forward with their careers and connect with art lovers around the globe.' }}</p>
                </div>
            </div>
        </section>

        <section class="bg-black py-20 text-white">
            <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-6 lg:grid-cols-2">
                <img src="{{ $heroImage }}" alt="ART Story artwork collection" class="aspect-[4/3] w-full object-cover">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.28em] text-white/55">Our goal</p>
                    <h2 class="mt-4 font-display text-4xl font-bold leading-tight">{{ $siteSettings?->about_goal_title ?: 'Creating global career opportunities through art.' }}</h2>
                    <p class="mt-6 text-lg leading-8 text-white/75">{{ $siteSettings?->about_goal_text ?: 'Our goal is to empower local talents and create global career opportunities for them in the world of art.' }}</p>
                </div>
            </div>
        </section>

        <section class="bg-gray-50 py-20">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-6 lg:grid-cols-3">
                <article class="bg-white p-8 shadow-sm ring-1 ring-gray-200">
                    <h3 class="font-display text-2xl font-bold text-black">{{ $siteSettings?->about_problem_title ?: 'What problem are we solving?' }}</h3>
                    <p class="mt-5 leading-7 text-gray-700">{{ $siteSettings?->about_problem_text ?: 'ART Story helps artists reach audiences, collectors, and exhibitions with more confidence.' }}</p>
                </article>
                <article class="bg-white p-8 shadow-sm ring-1 ring-gray-200">
                    <h3 class="font-display text-2xl font-bold text-black">{{ $siteSettings?->about_offer_title ?: 'What do we offer?' }}</h3>
                    <p class="mt-5 leading-7 text-gray-700">{{ $siteSettings?->about_offer_text ?: 'We bring artists closer to the world and help them showcase their art on international platforms.' }}</p>
                </article>
                <article class="bg-white p-8 shadow-sm ring-1 ring-gray-200">
                    <h3 class="font-display text-2xl font-bold text-black">{{ $siteSettings?->about_csr_title ?: 'Our CSR' }}</h3>
                    <p class="mt-5 leading-7 text-gray-700">{{ $siteSettings?->about_csr_text ?: 'ART Story believes in contributing to society and pledges part of its proceeds to non-profit work.' }}</p>
                </article>
            </div>
        </section>

        <section class="bg-white py-20">
            <div class="mx-auto grid max-w-5xl grid-cols-1 gap-8 px-6 text-center md:grid-cols-3">
                <div class="border border-gray-200 p-8">
                    <div class="font-display text-5xl font-bold text-black">{{ number_format($artistCount) }}</div>
                    <h4 class="mt-4 font-bold uppercase tracking-wide text-gray-600">Artists</h4>
                </div>
                <div class="border border-gray-200 p-8">
                    <div class="font-display text-5xl font-bold text-black">{{ number_format($artworkCount) }}</div>
                    <h4 class="mt-4 font-bold uppercase tracking-wide text-gray-600">Artworks</h4>
                </div>
                <div class="border border-gray-200 p-8">
                    <div class="font-display text-5xl font-bold text-black">Global</div>
                    <h4 class="mt-4 font-bold uppercase tracking-wide text-gray-600">Audience</h4>
                </div>
            </div>
        </section>
    </main>
</x-artstory.layout>
