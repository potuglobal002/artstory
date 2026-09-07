<x-frontend.layout :site-settings="$siteSettings" title="Course Checkout" active="courses">
    <style>
        .checkout-hero{background:#192335;color:#fff;position:relative;overflow:hidden}
        .checkout-hero:after{content:"";position:absolute;inset:auto -10% -90px 52%;height:240px;background:radial-gradient(circle,rgba(243,128,32,.26),transparent 62%);pointer-events:none}
        .checkout-shell{display:grid;gap:24px;grid-template-columns:minmax(0,1fr) 430px;align-items:start}
        .checkout-panel{border:1px solid #e2e7ee;background:#fff;border-radius:18px;box-shadow:0 18px 56px rgba(25,35,53,.07)}
        .checkout-field{display:grid;gap:8px}
        .checkout-field span{font-weight:700;color:#192335}
        .checkout-input{width:100%;border:1px solid #dce4ee;border-radius:12px;background:#fff;padding:13px 14px;color:#192335;outline:none;transition:border-color .2s,box-shadow .2s}
        .checkout-input:focus{border-color:#f38020;box-shadow:0 0 0 4px rgba(243,128,32,.13)}
        .program-card{position:sticky;top:22px}
        .program-head{display:grid;grid-template-columns:88px 1fr;gap:16px;align-items:center}
        .program-thumb{height:88px;border-radius:14px;overflow:hidden;background:#192335;display:grid;place-items:center;color:#fff;font-size:13px;text-align:center}
        .program-thumb img{width:100%;height:100%;object-fit:cover}
        .summary-row{display:flex;justify-content:space-between;gap:16px;padding:12px 0;border-bottom:1px solid #edf1f5}
        .summary-row:last-child{border-bottom:0}
        .coupon-row{display:grid;grid-template-columns:1fr auto;gap:10px}
        .coupon-button{border:1px solid #f38020;background:#fff7ef;color:#f38020;border-radius:12px;padding:0 15px;font-weight:800}
        .gateway-card{position:relative;border:1px solid #dfe6ef;border-radius:14px;background:#fff;padding:14px;cursor:pointer;transition:transform .2s,border-color .2s,box-shadow .2s,background .2s}
        .gateway-card:hover{transform:translateY(-1px);border-color:#f38020;box-shadow:0 12px 28px rgba(25,35,53,.08)}
        .gateway-card:has(input:checked){border-color:#f38020;background:#fff8f2;box-shadow:0 12px 30px rgba(243,128,32,.13)}
        .gateway-card input{position:absolute;opacity:0;pointer-events:none}
        .gateway-logo{height:28px;max-width:124px;object-fit:contain}
        .gateway-fallback{height:34px;width:34px;border-radius:10px;background:#192335;color:#fff;display:grid;place-items:center;font-weight:800}
        .gateway-mark{width:22px;height:22px;border-radius:999px;border:1px solid #cfd8e5;display:grid;place-items:center;color:transparent;margin-left:auto;font-size:13px}
        .gateway-card:has(input:checked) .gateway-mark{background:#f38020;border-color:#f38020;color:#fff}
        .help-note{border:1px solid #f8d5b7;background:#fff7ef;border-radius:14px;padding:14px;color:#5a3a20}
        @media (max-width:1023px){.checkout-shell{grid-template-columns:1fr}.program-card{position:static}}
        @media (max-width:640px){.program-head{grid-template-columns:1fr}.program-thumb{height:150px}.checkout-panel{border-radius:16px}}
    </style>

    <section class="checkout-hero py-10">
        <div class="grid-drift opacity-[.10]"></div>
        <div class="shell relative">
            <nav class="text-[13px] text-white/55 flex items-center gap-2 flex-wrap">
                <a href="{{ route('landing.home') }}" class="hover:text-white">Home</a><span>/</span>
                <a href="{{ route('frontend.course.show', $course) }}" class="hover:text-white">{{ $course->title }}</a><span>/</span>
                <span class="text-white/85">Checkout</span>
            </nav>
            <div class="mt-6 max-w-3xl">
                <span class="eyebrow text-flame-soft">Course enrollment</span>
                <h1 class="mt-3 text-white text-[clamp(32px,4vw,48px)] leading-tight">Complete your admission</h1>
                <p class="mt-3 text-white/72 text-[17px] leading-relaxed">Fill in student details, confirm the program, then choose a payment gateway.</p>
            </div>
        </div>
    </section>

    <section class="section bg-mist">
        <div class="shell">
            <form method="POST" action="{{ route('checkout.store', $course) }}" class="checkout-shell">
                @csrf

                <section class="checkout-panel p-5 md:p-7">
                    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                        <div>
                            <span class="eyebrow">Student details</span>
                            <h2 class="mt-3 text-[28px] leading-tight">Student information</h2>
                            <p class="mt-2 text-muted">Please use an active phone number for admission updates.</p>
                        </div>
                        <span class="text-sm text-muted">* Required</span>
                    </div>

                    <div class="mt-7 grid gap-5 md:grid-cols-2">
                        <label class="checkout-field">
                            <span>Student name *</span>
                            <input name="student_name" value="{{ old('student_name') }}" required autocomplete="name" autofocus class="checkout-input" placeholder="Full name">
                            @error('student_name')<small class="text-flame">{{ $message }}</small>@enderror
                        </label>

                        <label class="checkout-field">
                            <span>Student phone *</span>
                            <input name="student_phone" value="{{ old('student_phone') }}" required inputmode="tel" autocomplete="tel" class="checkout-input" placeholder="01XXXXXXXXX">
                            @error('student_phone')<small class="text-flame">{{ $message }}</small>@enderror
                        </label>

                        <label class="checkout-field">
                            <span>Student email</span>
                            <input type="email" name="student_email" value="{{ old('student_email') }}" autocomplete="email" class="checkout-input" placeholder="student@example.com">
                            @error('student_email')<small class="text-flame">{{ $message }}</small>@enderror
                        </label>

                        <label class="checkout-field">
                            <span>Guardian phone</span>
                            <input name="guardian_phone" value="{{ old('guardian_phone') }}" inputmode="tel" autocomplete="tel" class="checkout-input" placeholder="Optional">
                            @error('guardian_phone')<small class="text-flame">{{ $message }}</small>@enderror
                        </label>
                    </div>

                    <div class="mt-5 grid gap-5">
                        <label class="checkout-field">
                            <span>Address</span>
                            <input name="address" value="{{ old('address') }}" autocomplete="street-address" class="checkout-input" placeholder="Area, city">
                            @error('address')<small class="text-flame">{{ $message }}</small>@enderror
                        </label>

                        <label class="checkout-field">
                            <span>Note</span>
                            <textarea name="note" rows="4" class="checkout-input" placeholder="Preferred batch, campus, or anything the admission team should know">{{ old('note') }}</textarea>
                            @error('note')<small class="text-flame">{{ $message }}</small>@enderror
                        </label>
                    </div>
                </section>

                <aside class="program-card grid gap-5">
                    <section class="checkout-panel p-5 md:p-6">
                        <span class="eyebrow">Program details</span>
                        <div class="program-head mt-4">
                            <div class="program-thumb">
                                @if ($course->imageUrl())
                                    <img src="{{ $course->imageUrl() }}" alt="{{ $course->title }}">
                                @else
                                    <span>{{ $course->title }}</span>
                                @endif
                            </div>
                            <div>
                                <div class="mono text-[12px] tracking-[.2em] uppercase text-flame">{{ $course->category ?: 'Course' }}</div>
                                <h2 class="mt-2 text-[24px] leading-tight">{{ $course->title }}</h2>
                                <p class="mt-2 text-sm text-muted leading-relaxed">{{ $course->excerpt }}</p>
                            </div>
                        </div>

                        <dl class="mt-5">
                            <div class="summary-row"><dt class="text-muted">Course fee</dt><dd class="font-display text-[28px] leading-none text-ink">{{ $course->feeLabel() }}</dd></div>
                            <div class="summary-row"><dt class="text-muted">Duration</dt><dd class="font-semibold text-right">{{ $course->duration ?: 'Confirm with admin' }}</dd></div>
                            <div class="summary-row"><dt class="text-muted">Classes</dt><dd class="font-semibold text-right">{{ $course->sessions ?: 'Confirm with admin' }}</dd></div>
                            <div class="summary-row"><dt class="text-muted">Campus</dt><dd class="font-semibold text-right">{{ $course->campus ?: 'STS Institute' }}</dd></div>
                        </dl>

                        <div class="mt-5 border-t border-line pt-5">
                            <label class="checkout-field">
                                <span>Coupon code</span>
                                <div class="coupon-row">
                                    <input name="coupon_code" value="{{ old('coupon_code') }}" class="checkout-input uppercase" placeholder="Enter coupon">
                                    <button type="submit" class="coupon-button">Apply</button>
                                </div>
                                @error('coupon_code')<small class="text-flame">{{ $message }}</small>@enderror
                                <small class="text-muted">Valid course coupons will be applied before payment.</small>
                            </label>
                        </div>
                    </section>

                    <section class="checkout-panel p-5 md:p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="eyebrow">Payment method</span>
                                <h2 class="mt-3 text-[24px] leading-tight">Select gateway</h2>
                            </div>
                            <span class="text-sm text-muted">Sandbox</span>
                        </div>

                        <div class="mt-5 grid gap-3">
                            @forelse ($gateways as $gateway)
                                @php
                                    $gatewayInitial = strtoupper(substr($gateway->name, 0, 1));
                                    $extra = $gateway->extra_config ?: [];
                                @endphp
                                <label class="gateway-card">
                                    <input type="radio" name="gateway_code" value="{{ $gateway->code }}" required @checked(old('gateway_code', $loop->first ? $gateway->code : null) === $gateway->code)>
                                    <div class="flex items-center gap-3">
                                        @if ($gateway->logo_url)
                                            <img src="{{ $gateway->logo_url }}" alt="{{ $gateway->name }}" class="gateway-logo">
                                        @else
                                            <span class="gateway-fallback">{{ $gatewayInitial }}</span>
                                        @endif
                                        <div class="min-w-0">
                                            <strong class="block text-ink">{{ $gateway->name }}</strong>
                                            <span class="block text-sm text-muted">{{ $gateway->is_sandbox ? 'Sandbox' : 'Live' }}{{ $gateway->use_sandbox_simulator ? ' simulator' : '' }}</span>
                                        </div>
                                        <span class="gateway-mark">&check;</span>
                                    </div>
                                    @if ($gateway->code === 'bkash' && $gateway->is_sandbox)
                                        <span class="mt-3 block text-[12px] text-muted">Test OTP {{ $extra['sandbox_otp'] ?? '123456' }} &middot; PIN {{ $extra['sandbox_pin'] ?? '12121' }}</span>
                                    @endif
                                </label>
                            @empty
                                <div class="rounded-xl border border-line bg-white p-4 text-muted">No active payment gateway found. Please enable one from admin.</div>
                            @endforelse
                        </div>
                        @error('gateway_code')<span class="mt-3 block text-flame text-sm">{{ $message }}</span>@enderror

                        <div class="help-note mt-5">
                            <strong class="block text-ink">Need help?</strong>
                            <p class="mt-1 text-sm leading-relaxed">Call admission and mention {{ $course->title }}.</p>
                            @if ($siteSettings?->contact_phone)
                                <a href="tel:{{ $siteSettings->contact_phone }}" class="mt-2 inline-flex font-bold text-flame">{{ $siteSettings->contact_phone }}</a>
                            @endif
                        </div>

                        <button class="btn btn-primary w-full mt-5" @disabled($gateways->isEmpty())>Continue to payment</button>
                        <p class="mt-3 text-center text-xs text-muted">Enrollment stays pending until the gateway confirms payment.</p>
                    </section>
                </aside>
            </form>
        </div>
    </section>
</x-frontend.layout>
