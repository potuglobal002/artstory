<x-artstory.layout :site-settings="$siteSettings" title="Artist Registration" active="artist_portal">
    <main class="mx-auto max-w-7xl px-6 py-14">
        <style>
            .portal-shell { display: grid; gap: 54px; grid-template-columns: minmax(280px, .75fr) minmax(0, 1.25fr); }
            .portal-kicker { color: #88884d; font-size: 12px; font-weight: 900; letter-spacing: .22em; text-transform: uppercase; }
            .portal-title { color: #111827; font-family: "Playfair Display", serif; font-size: clamp(38px, 5vw, 72px); font-weight: 600; line-height: .98; margin-top: 18px; }
            .portal-copy { color: #5f6673; font-size: 16px; line-height: 1.85; margin-top: 24px; }
            .portal-card { border: 1px solid #e5e7eb; background: #fff; padding: 32px; }
            .portal-form { display: grid; gap: 18px; }
            .portal-field span { color: #111827; display: block; font-size: 12px; font-weight: 900; letter-spacing: .12em; margin-bottom: 8px; text-transform: uppercase; }
            .portal-field input,
            .portal-field textarea { border: 1px solid #d1d5db; color: #111827; font-size: 15px; min-height: 48px; outline: 0; padding: 12px 14px; width: 100%; }
            .portal-field textarea { min-height: 150px; resize: vertical; }
            .portal-field input:focus,
            .portal-field textarea:focus { border-color: #111827; }
            .portal-submit { align-items: center; background: #111; color: #fff; display: inline-flex; font-size: 12px; font-weight: 900; gap: 8px; justify-content: center; letter-spacing: .12em; min-height: 50px; padding: 0 22px; text-transform: uppercase; }
            .portal-alert { border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; font-size: 14px; line-height: 1.6; padding: 14px 16px; }
            .portal-errors { border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; font-size: 14px; line-height: 1.6; padding: 14px 16px; }
            @media (max-width: 900px) { .portal-shell { grid-template-columns: 1fr; } }
        </style>

        <div class="portal-shell">
            <section>
                <p class="portal-kicker">Artist Access</p>
                <h1 class="portal-title">Register with ART Story.</h1>
                <p class="portal-copy">Submit your artist profile for review. After approval, ART Story will email your login details so you can upload artworks from your own dashboard.</p>
                <p class="portal-copy">Already approved? <a href="{{ route('artist.login') }}" class="font-bold text-[#88884d] underline underline-offset-4">Log in here</a>.</p>
            </section>

            <section class="portal-card">
                @if (session('status'))
                    <div class="portal-alert mb-6">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="portal-errors mb-6">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('artist.register.store') }}" enctype="multipart/form-data" class="portal-form">
                    @csrf
                    <label class="portal-field">
                        <span>Full name</span>
                        <input name="name" value="{{ old('name') }}" required>
                    </label>
                    <label class="portal-field">
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                    </label>
                    <label class="portal-field">
                        <span>Phone</span>
                        <input name="phone" value="{{ old('phone') }}">
                    </label>
                    <label class="portal-field">
                        <span>Nationality</span>
                        <input name="nationality" value="{{ old('nationality', 'Bangladeshi') }}">
                    </label>
                    <label class="portal-field">
                        <span>Birth place</span>
                        <input name="birth_place" value="{{ old('birth_place') }}">
                    </label>
                    <label class="portal-field">
                        <span>Artist picture</span>
                        <input type="file" name="picture" accept="image/*">
                    </label>
                    <label class="portal-field">
                        <span>Biography</span>
                        <textarea name="biography">{{ old('biography') }}</textarea>
                    </label>
                    <button class="portal-submit">
                        Submit registration
                        <span class="material-symbols-rounded">arrow_forward</span>
                    </button>
                </form>
            </section>
        </div>
    </main>
</x-artstory.layout>
