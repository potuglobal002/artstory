<x-artstory.layout :site-settings="$siteSettings" title="Artist Login" active="artist_portal">
    <main class="mx-auto max-w-5xl px-6 py-14">
        <style>
            .login-wrap { display: grid; gap: 42px; grid-template-columns: minmax(260px, .8fr) minmax(0, 1fr); }
            .login-card { border: 1px solid #e5e7eb; background: #fff; padding: 34px; }
            .login-kicker { color: #88884d; font-size: 12px; font-weight: 900; letter-spacing: .22em; text-transform: uppercase; }
            .login-title { font-family: "Playfair Display", serif; font-size: clamp(36px, 4vw, 56px); font-weight: 600; line-height: 1; margin-top: 18px; }
            .login-copy { color: #5f6673; line-height: 1.8; margin-top: 22px; }
            .login-form { display: grid; gap: 18px; }
            .login-field span { display: block; font-size: 12px; font-weight: 900; letter-spacing: .12em; margin-bottom: 8px; text-transform: uppercase; }
            .login-field input { border: 1px solid #d1d5db; min-height: 50px; outline: 0; padding: 12px 14px; width: 100%; }
            .login-field input:focus { border-color: #111827; }
            .login-submit { background: #111; color: #fff; font-size: 12px; font-weight: 900; letter-spacing: .12em; min-height: 50px; padding: 0 22px; text-transform: uppercase; }
            .login-errors { border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; font-size: 14px; line-height: 1.6; padding: 14px 16px; }
            @media (max-width: 820px) { .login-wrap { grid-template-columns: 1fr; } }
        </style>

        <div class="login-wrap">
            <section>
                <p class="login-kicker">Artist Portal</p>
                <h1 class="login-title">Welcome back.</h1>
                <p class="login-copy">Use the email and temporary password sent after admin approval. From here you can submit new artwork for ART Story review.</p>
                <p class="login-copy">Need access? <a href="{{ route('artist.register') }}" class="font-bold text-[#88884d] underline underline-offset-4">Register as an artist</a>.</p>
            </section>

            <section class="login-card">
                @if ($errors->any())
                    <div class="login-errors mb-6">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('artist.login.store') }}" class="login-form">
                    @csrf
                    <label class="login-field">
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </label>
                    <label class="login-field">
                        <span>Password</span>
                        <input type="password" name="password" required>
                    </label>
                    <label class="flex items-center gap-3 text-sm text-gray-600">
                        <input type="checkbox" name="remember" value="1">
                        Remember me
                    </label>
                    <button class="login-submit">Log in</button>
                </form>
            </section>
        </div>
    </main>
</x-artstory.layout>
