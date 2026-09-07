<x-frontend.layout :site-settings="$siteSettings" title="Payment" active="courses">
    <section class="section bg-mist">
        <div class="shell max-w-3xl">
            <div class="card p-8 text-center">
                @if (session('payment_error'))
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-left text-red-700">{{ session('payment_error') }}</div>
                @endif
                @if ($gateway?->logo_url)
                    <img src="{{ $gateway->logo_url }}" alt="{{ $gateway->name }}" class="mx-auto mb-5 h-10 w-auto object-contain">
                @endif
                <span class="eyebrow">{{ $gateway?->name ?: $enrollment->gateway_name }}</span>
                <h1 class="mt-5 text-[clamp(30px,5vw,46px)]">Sandbox payment</h1>
                <p class="mt-4 text-muted">This is the initial sandbox flow. When live merchant credentials are approved, this step can redirect to the real gateway checkout URL.</p>
                <div class="mt-8 rounded-2xl bg-mist border border-line p-6 grid gap-3 text-left">
                    <div class="flex justify-between gap-4"><span class="text-muted">Student</span><strong>{{ $enrollment->student_name }}</strong></div>
                    <div class="flex justify-between gap-4"><span class="text-muted">Course</span><strong>{{ $enrollment->course->title }}</strong></div>
                    <div class="flex justify-between gap-4"><span class="text-muted">Amount</span><strong>BDT {{ number_format((float) $enrollment->amount, 0) }}</strong></div>
                    <div class="flex justify-between gap-4"><span class="text-muted">Gateway</span><strong>{{ $enrollment->gateway_name }}</strong></div>
                </div>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <form method="POST" action="{{ route('checkout.sandbox-success', $enrollment) }}">
                        @csrf
                        <button class="btn btn-primary">Pay in sandbox</button>
                    </form>
                    <a href="{{ route('checkout.fail', $enrollment) }}" class="btn btn-ghost">Cancel payment</a>
                </div>
            </div>
        </div>
    </section>
</x-frontend.layout>
