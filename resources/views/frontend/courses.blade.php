<x-frontend.layout :site-settings="$siteSettings" title="All Courses" active="courses">
    <section class="bg-ink text-white pt-16 pb-20 md:pt-24 md:pb-24 relative overflow-hidden">
        <div class="grid-drift opacity-[.14]"></div>
        <div class="shell relative">
            <span class="eyebrow on-dark">All courses</span>
            <h1 class="mt-6 text-white text-[clamp(34px,5.6vw,56px)] max-w-3xl">Programs with the right pace, outcome and support.</h1>
            <p class="mt-6 text-white/75 text-[17px] leading-relaxed max-w-2xl">Not sure which? Take the free placement assessment and we will point at one.</p>
            <div class="mt-9 flex flex-wrap gap-3"><a href="{{ route('frontend.contact') }}" class="btn btn-primary">Book a placement assessment</a></div>
        </div>
    </section>
    <section class="section bg-white">
        <div class="shell">
            <div class="mt-2 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($courses as $course)
                    <a href="{{ route('frontend.course.show', $course) }}" class="card p-6 flex flex-col group" data-reveal>
                        <div class="card-thumb"><img src="{{ $course->imageUrl() ?: asset('theme/sts-website/assets/img/course-ielts-academic.svg') }}" alt="{{ $course->title }}" loading="lazy"></div>
                        <div class="mt-4">
                            <div class="flex items-start justify-between gap-3">
                                <span class="mono text-[11px] tracking-[.16em] uppercase text-muted">{{ $course->category }}</span>
                                @if ($course->badge)<span class="mono text-[10px] tracking-[.14em] uppercase px-2 py-1 rounded-md bg-ink text-white">{{ $course->badge }}</span>@endif
                            </div>
                            <h3 class="mt-3 text-[21px] leading-tight">{{ $course->title }}</h3>
                        </div>
                        <p class="mt-2.5 text-[14.5px] text-muted leading-relaxed flex-1">{{ $course->excerpt }}</p>
                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            @if ($course->target_score)<span class="band-chip">{{ $course->target_score }}</span>@endif
                            @if ($course->duration)<span class="text-[13px] text-muted">{{ $course->duration }}</span>@endif
                            @if ($course->sessions)<span class="w-1 h-1 rounded-full bg-line"></span><span class="text-[13px] text-muted">{{ $course->sessions }}</span>@endif
                        </div>
                        <div class="mt-5 pt-5 border-t border-line flex items-center justify-between">
                            <span class="text-[15px] font-semibold">{{ $course->fee ? 'BDT ' . $course->fee : 'Details' }}</span>
                            <span class="text-flame text-[14px] font-semibold">View course →</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <p class="mt-8 mono text-[12px] tracking-[.14em] uppercase text-muted">Showing {{ $courses->count() }} programs</p>
        </div>
    </section>
    <section class="section bg-mist">
        <div class="shell">
            <span class="eyebrow">Side by side</span>
            <h2 class="h-sec mt-5 max-w-xl">Compare the most-asked-about programs</h2>
            <div class="mt-10 overflow-x-auto rounded-2xl border border-line bg-white">
                <table class="w-full text-[14.5px] min-w-[640px]">
                    <thead><tr class="bg-ink text-white text-left"><th class="p-4">Course</th><th class="p-4">Duration</th><th class="p-4">Classes</th><th class="p-4">Target</th></tr></thead>
                    <tbody>
                        @foreach ($courses->take(5) as $course)
                            <tr class="border-b border-line"><td class="p-4 font-semibold">{{ $course->title }}</td><td class="p-4 text-muted">{{ $course->duration }}</td><td class="p-4 text-muted">{{ $course->sessions }}</td><td class="p-4 text-muted">{{ $course->target_score }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-frontend.layout>
