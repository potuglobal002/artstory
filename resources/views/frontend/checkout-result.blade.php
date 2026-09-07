<x-frontend.layout :site-settings="$siteSettings" title="Payment {{ $status }}" active="courses">
    <section class="section bg-mist">
        <div class="shell max-w-3xl">
            <div class="card p-8 text-center">
                <span class="eyebrow">{{ $status === 'success' ? 'Payment confirmed' : 'Payment not completed' }}</span>
                <h1 class="mt-5 text-[clamp(30px,5vw,46px)]">{{ $status === 'success' ? 'Your enrolment is booked' : 'Payment was cancelled' }}</h1>
                <p class="mt-4 text-muted">
                    {{ $status === 'success' ? 'The admin team can now see this enrolment and payment details in the dashboard.' : 'You can retry payment from the course page.' }}
                </p>
                <div class="mt-8 rounded-2xl bg-mist border border-line p-6 grid gap-3 text-left">
                    <div class="flex justify-between gap-4"><span class="text-muted">Course</span><strong>{{ $enrollment->course->title }}</strong></div>
                    <div class="flex justify-between gap-4"><span class="text-muted">Amount</span><strong>BDT {{ number_format((float) $enrollment->amount, 0) }}</strong></div>
                    <div class="flex justify-between gap-4"><span class="text-muted">Status</span><strong>{{ ucfirst($enrollment->payment_status) }}</strong></div>
                    @if ($enrollment->transaction_id)
                        <div class="flex justify-between gap-4"><span class="text-muted">Transaction ID</span><strong>{{ $enrollment->transaction_id }}</strong></div>
                    @endif
                </div>
                <a href="{{ route('frontend.course.show', $enrollment->course) }}" class="btn btn-primary mt-8">Back to course</a>
            </div>
        </div>
    </section>
</x-frontend.layout>
