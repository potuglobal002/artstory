@php
    $normalizeList = fn ($items, $key = 'text') => collect($items ?: [])
        ->map(fn ($item) => is_array($item) ? ($item[$key] ?? null) : $item)
        ->filter()
        ->values();

    $defaultIncludes = collect([
        'Course book + STS practice pack',
        '4 full mock tests (LRW + Speaking)',
        'Individual score review',
        'IELTS registration support at our venue',
        'Result day guidance',
    ]);

    $defaultOutcomes = collect([
        'Sit the exam knowing exactly how each section is scored',
        'Write Task 1 and Task 2 inside the 20/40 minute split',
        'Hold a two-minute Part 2 answer without drying up',
        'Read three passages in 60 minutes without panic',
    ]);

    $defaultModules = collect([
        ['title' => 'Listening', 'description' => 'Section-by-section strategy, accent exposure, map and diagram labelling, note-completion traps.'],
        ['title' => 'Reading', 'description' => 'Skimming and scanning drills, True/False/Not Given logic, matching headings and 60-minute pacing.'],
        ['title' => 'Writing Task 1', 'description' => 'Line graphs, bar charts, pie charts, tables, processes and maps with clear overview writing.'],
        ['title' => 'Writing Task 2', 'description' => 'Essay types, thesis and topic sentences, argument development, cohesion and band descriptors.'],
        ['title' => 'Speaking', 'description' => 'Part 1 fluency, Part 2 cue card structure, Part 3 abstract discussion and playback feedback.'],
        ['title' => 'Mock & review', 'description' => 'Four full mock tests under exam conditions, each followed by an individual score breakdown session.'],
    ]);

    $defaultSchedule = collect([
        ['batch' => 'Morning', 'days' => 'Sat Mon Wed', 'time' => '8:00 - 9:30 AM'],
        ['batch' => 'Afternoon', 'days' => 'Sun Tue Thu', 'time' => '4:00 - 5:30 PM'],
        ['batch' => 'Evening', 'days' => 'Sat Mon Wed', 'time' => '6:30 - 8:00 PM'],
    ]);

    $defaultFaq = collect([
        ['question' => 'Do I need a certain level to join?', 'answer' => 'No. We take a short placement assessment first and suggest the right batch for your current level.'],
        ['question' => 'Can I take the exam at STS?', 'answer' => 'STS Institute supports IELTS registration and test venue guidance from the admission desk.'],
        ['question' => 'What if I miss classes?', 'answer' => 'Talk with the department coordinator. We help students recover missed lessons through support sessions where possible.'],
    ]);

    $includes = $normalizeList($course->includes)->isNotEmpty() ? $normalizeList($course->includes) : $defaultIncludes;
    $outcomes = $normalizeList($course->outcomes)->isNotEmpty() ? $normalizeList($course->outcomes) : $defaultOutcomes;
    $modules = collect($course->modules ?: [])->filter(fn ($item) => filled($item['title'] ?? null))->values();
    $modules = $modules->isNotEmpty() ? $modules : $defaultModules;
    $schedule = collect($course->schedule ?: [])->filter(fn ($item) => filled($item['batch'] ?? null))->values();
    $schedule = $schedule->isNotEmpty() ? $schedule : $defaultSchedule;
    $faq = collect($course->faq ?: [])->filter(fn ($item) => filled($item['question'] ?? null))->values();
    $faq = $faq->isNotEmpty() ? $faq : $defaultFaq;

    $fee = $course->feeLabel();
    $lead = $course->lead ?: $course->excerpt;
    $whoFor = $course->who_for ?: 'You need ' . $course->title . ' guidance with a clear plan, structured classes and measurable progress before admission, exam or career decisions.';
    $targetScore = $course->target_score ?: 'Target';
    $bandHit = str_contains($targetScore, '7') ? 14 : 13;
@endphp

<x-frontend.layout :site-settings="$siteSettings" :title="$course->title" active="courses">
    <section class="bg-ink text-white pt-16 pb-20 md:pt-20 md:pb-24 relative overflow-hidden">
        <div class="grid-drift opacity-[.14]"></div>
        <div class="shell relative grid gap-12 lg:grid-cols-[1.35fr_1fr] lg:gap-16 items-start">
            <div>
                <nav class="text-[13px] text-white/50 flex items-center gap-2 flex-wrap">
                    <a href="{{ route('landing.home') }}" class="hover:text-white">Home</a><span>/</span>
                    <a href="{{ route('frontend.courses') }}" class="hover:text-white">Courses</a><span>/</span>
                    <span class="text-white/80">{{ $course->title }}</span>
                </nav>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <span class="mono text-[11px] tracking-[.16em] uppercase text-flame-soft">{{ $course->category ?: 'Course' }}</span>
                    @if ($course->badge)
                        <span class="mono text-[10px] tracking-[.14em] uppercase px-2 py-1 rounded-md bg-flame text-white">{{ $course->badge }}</span>
                    @endif
                </div>
                <h1 class="mt-4 text-white text-[clamp(32px,5.2vw,54px)]">{{ $course->title }}</h1>
                <p class="mt-5 text-white/80 text-[17px] leading-relaxed max-w-xl">{{ $lead }}</p>
                <div class="mt-9 bandrail text-white/45" data-rail="19" data-hit="{{ $bandHit }}"></div>
            </div>

            <aside class="rounded-2xl bg-white text-ink p-7 shadow-[0_18px_50px_rgba(0,0,0,.28)]">
                <div class="flex items-start justify-between gap-5">
                    <div>
                        <div class="font-display text-[38px] leading-none">{{ $fee }}</div>
                        <p class="text-[13px] text-muted mt-1.5">{{ $course->fee_note ?: 'Instalments available - mock tests included' }}</p>
                    </div>
                    <span class="mono text-[12px] text-flame-deep bg-ember border border-[#ffd9b8] px-3.5 py-2 rounded-full whitespace-nowrap">{{ $targetScore }}</span>
                </div>
                <dl class="mt-6 grid gap-3 text-[14.5px] border-t border-line pt-6">
                    <div class="flex justify-between gap-4"><dt class="text-muted">Duration</dt><dd class="font-medium">{{ $course->duration ?: '3 months' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-muted">Classes</dt><dd class="font-medium">{{ $course->sessions ?: '36 classes' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-muted">Batch size</dt><dd class="font-medium">{{ $course->batch_size ?: '12-16 students' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-muted">Campus</dt><dd class="font-medium">{{ $course->campus ?: 'Narsingdi Sadar' }}</dd></div>
                </dl>
                <div class="mt-6 grid gap-2.5">
                    <a href="{{ route('checkout.create', $course) }}" class="btn btn-primary w-full">Enrol in this course</a>
                    <a href="#syllabus" class="btn btn-ghost w-full">Full syllabus &amp; fees</a>
                </div>
                <p class="mt-4 text-[13px] text-muted text-center">Or call the PR desk on <a href="tel:01901402202" class="text-flame-deep font-medium">01901-402202</a></p>
            </aside>
        </div>
    </section>

    <section class="section bg-white">
        <div class="shell grid gap-12 lg:grid-cols-[1fr_1.1fr] lg:gap-20">
            <div>
                <span class="eyebrow">Who this is for</span>
                <p class="mt-6 text-[19px] leading-relaxed text-ink font-medium">{{ $whoFor }}</p>
                <p class="mt-5 text-[15.5px] text-muted leading-relaxed">{{ $course->excerpt }}</p>
                <div class="mt-8 rounded-xl bg-mist border border-line p-5">
                    <p class="text-[14.5px] text-muted leading-relaxed">Not certain this is your level? The free placement assessment takes 25 minutes and settles it before you pay anything.</p>
                    <a href="{{ route('frontend.contact') }}" class="btn btn-ghost btn-sm mt-4">Book the assessment</a>
                </div>
            </div>
            <div>
                <span class="eyebrow">What you will be able to do</span>
                <ul class="mt-6 grid gap-3.5">
                    @foreach ($outcomes as $outcome)
                        <li class="flex gap-3.5 items-start">
                            <span class="mt-1 w-5 h-5 rounded-full bg-ember grid place-items-center shrink-0 text-flame">&#10003;</span>
                            <span class="text-[15.5px] leading-relaxed text-[#2c3a52]">{{ $outcome }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-9 pt-8 border-t border-line">
                    <span class="eyebrow">Included</span>
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach ($includes as $include)
                            <span class="text-[13.5px] px-3 py-1.5 rounded-lg bg-mist border border-line text-muted">{{ $include }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="syllabus" class="section bg-mist">
        <div class="shell">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div>
                    <span class="eyebrow">Course structure</span>
                    <h2 class="h-sec mt-5 max-w-lg">{{ $modules->count() }} modules, taught in this order</h2>
                </div>
                <a href="{{ route('frontend.contact') }}" class="btn btn-ghost btn-sm">Week-by-week breakdown</a>
            </div>
            <div class="mt-11 grid gap-4 md:grid-cols-2">
                @foreach ($modules as $module)
                    <div class="card-flat p-6 flex gap-5" data-reveal>
                        <span class="mono text-[12px] tracking-[.14em] text-flame pt-1 shrink-0">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3 class="text-[18px]">{{ $module['title'] }}</h3>
                            <p class="mt-2 text-[14.5px] text-muted leading-relaxed">{{ $module['description'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section bg-white">
        <div class="shell grid gap-12 lg:grid-cols-[1fr_1.2fr] lg:gap-20">
            <div>
                <span class="eyebrow">Batches</span>
                <h2 class="h-sec mt-5">Pick the batch that fits your week</h2>
                <p class="lede mt-5">Batches open monthly. Seats are limited by the class cap, so the popular slots close early - the morning batch usually goes first.</p>
                <a href="{{ route('frontend.contact') }}" class="btn btn-primary mt-8">Reserve a seat</a>
            </div>
            <div class="grid gap-3">
                @foreach ($schedule as $slot)
                    <div class="card-flat p-6 flex flex-wrap items-center justify-between gap-4" data-reveal>
                        <div>
                            <h3 class="text-[18px]">{{ $slot['batch'] }}</h3>
                            <p class="text-[14px] text-muted mt-1">{{ $slot['days'] ?? '' }}</p>
                        </div>
                        <span class="mono text-[14px] text-flame-deep bg-ember border border-[#ffd9b8] px-3.5 py-2 rounded-lg">{{ $slot['time'] ?? '' }}</span>
                    </div>
                @endforeach
                <p class="text-[13.5px] text-muted mt-2">Times are indicative and confirmed at admission. Friday timings differ for Junior batches.</p>
            </div>
        </div>
    </section>

    <section class="section bg-ink text-white">
        <div class="shell grid gap-12 lg:grid-cols-[1fr_1.3fr] lg:gap-20">
            <div>
                <span class="eyebrow on-dark">Questions</span>
                <h2 class="h-sec mt-5 text-white">What students ask before joining</h2>
                <p class="mt-5 text-white/70 text-[15.5px] leading-relaxed">Anything not covered here, the IELTS Department answers on 01901-402204.</p>
            </div>
            <div class="grid gap-3">
                @foreach ($faq as $item)
                    <details class="acc rounded-xl border border-white/12 bg-white/[.04] p-5">
                        <summary class="flex items-center justify-between gap-5 text-[16.5px] font-semibold text-white">
                            {{ $item['question'] }}
                            <span class="chev shrink-0">v</span>
                        </summary>
                        <p class="mt-3.5 text-[15px] text-white/70 leading-relaxed">{{ $item['answer'] ?? '' }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section bg-white">
        <div class="shell">
            <span class="eyebrow">Also consider</span>
            <h2 class="h-sec mt-5 max-w-lg">Other programs students pair with this one</h2>
            <div class="mt-11 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($courses as $related)
                    <a href="{{ route('frontend.course.show', $related) }}" class="card p-6" data-reveal>
                        <div class="aspect-[4/3] rounded-xl overflow-hidden bg-ink grid place-items-center text-white text-[22px] font-display">
                            @if ($related->imageUrl())
                                <img src="{{ $related->imageUrl() }}" alt="{{ $related->title }}" class="w-full h-full object-cover">
                            @else
                                {{ $related->title }}
                            @endif
                        </div>
                        <div class="mt-5 flex items-center justify-between gap-4">
                            <span class="mono text-[11px] tracking-[.14em] uppercase text-muted">{{ $related->category ?: 'Course' }}</span>
                            @if ($related->badge)
                                <span class="mono text-[10px] tracking-[.12em] uppercase rounded-md bg-ink text-white px-2 py-1">{{ $related->badge }}</span>
                            @endif
                        </div>
                        <h3 class="mt-4 text-[20px]">{{ $related->title }}</h3>
                        <p class="mt-2 text-muted">{{ $related->excerpt }}</p>
                        <div class="mt-6 flex flex-wrap items-center gap-2 text-[13px] text-muted">
                            <span class="text-flame-deep bg-ember border border-[#ffd9b8] px-2.5 py-1 rounded-full">{{ $related->target_score ?: 'All levels' }}</span>
                            <span>{{ $related->duration }}</span>
                            <span>{{ $related->sessions }}</span>
                        </div>
                        <div class="mt-6 pt-5 border-t border-line flex items-center justify-between">
                            <strong>{{ $related->fee ? 'BDT ' . $related->fee : 'Details' }}</strong>
                            <span class="text-flame-deep font-semibold">View course &rarr;</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-frontend.layout>
