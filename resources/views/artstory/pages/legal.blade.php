<x-artstory.layout :site-settings="$siteSettings" :title="$title">
    <main>
        <section class="border-b border-gray-200 bg-[#f5f4f1] py-16 md:py-24">
            <div class="mx-auto max-w-5xl px-6">
                <p class="text-xs font-black uppercase tracking-[0.24em] text-[#88884d]">ART Story</p>
                <h1 class="mt-4 font-display text-5xl font-semibold leading-tight text-black md:text-6xl">{{ $title }}</h1>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <article class="legal-page-copy mx-auto max-w-4xl px-6 text-[17px] leading-8 text-gray-700">
                {!! $content !!}
            </article>
        </section>
    </main>

    <style>
        .legal-page-copy > * + * { margin-top: 1.4rem; }
        .legal-page-copy h2 { color: #111827; font-family: var(--font-display); font-size: 1.8rem; font-weight: 600; line-height: 1.2; margin-top: 2.75rem; }
        .legal-page-copy h3 { color: #111827; font-size: 1.1rem; font-weight: 800; margin-top: 2.25rem; }
        .legal-page-copy ul, .legal-page-copy ol { margin: 1.25rem 0; padding-left: 1.4rem; }
        .legal-page-copy ul { list-style: disc; }
        .legal-page-copy ol { list-style: decimal; }
        .legal-page-copy a { color: #111827; font-weight: 700; text-decoration: underline; text-underline-offset: 4px; }
    </style>
</x-artstory.layout>
