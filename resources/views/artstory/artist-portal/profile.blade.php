<x-artstory.layout :site-settings="$siteSettings" title="Profile Settings" active="artist_portal">
    <main class="mx-auto max-w-4xl px-6 py-12">
        <style>
            .artist-head { align-items: end; border-bottom: 1px solid #e5e7eb; display: flex; gap: 24px; justify-content: space-between; margin-bottom: 34px; padding-bottom: 26px; }
            .artist-kicker { color: #88884d; font-size: 12px; font-weight: 900; letter-spacing: .22em; margin-bottom: 12px; text-transform: uppercase; }
            .artist-title { font-family: "Playfair Display", serif; font-size: clamp(36px, 4vw, 58px); font-weight: 600; line-height: 1; }
            .artist-btn { align-items: center; border: 1px solid #111827; display: inline-flex; font-size: 12px; font-weight: 900; justify-content: center; letter-spacing: .12em; min-height: 44px; padding: 0 18px; text-transform: uppercase; }
            .artist-panel { border: 1px solid #e5e7eb; background: #fff; padding: 28px; }
            .artist-form { display: grid; gap: 16px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .artist-field span { display: block; font-size: 11px; font-weight: 900; letter-spacing: .12em; margin-bottom: 8px; text-transform: uppercase; }
            .artist-field input,
            .artist-field textarea { border: 1px solid #d1d5db; min-height: 46px; outline: 0; padding: 11px 12px; width: 100%; }
            .artist-field small { color: #6b7280; display: block; font-size: 12px; line-height: 1.5; margin-top: 7px; }
            .artist-field textarea { min-height: 150px; resize: vertical; }
            .artist-submit { background: #111; color: #fff; font-size: 12px; font-weight: 900; letter-spacing: .12em; min-height: 48px; text-transform: uppercase; }
            .artist-status { border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534; font-size: 14px; line-height: 1.6; margin-bottom: 24px; padding: 14px 16px; }
            .artist-errors { border: 1px solid #fecaca; background: #fef2f2; color: #991b1b; font-size: 14px; line-height: 1.6; margin-bottom: 24px; padding: 14px 16px; }
            @media (max-width: 760px) { .artist-head, .artist-form { display: grid; grid-template-columns: 1fr; } }
        </style>

        <header class="artist-head">
            <div>
                <p class="artist-kicker">Artist Dashboard</p>
                <h1 class="artist-title">Profile Settings</h1>
            </div>
            <a href="{{ route('artist.dashboard') }}" class="artist-btn">Back to dashboard</a>
        </header>

        @if (session('status'))
            <div class="artist-status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="artist-errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="artist-panel">
            <form method="POST" action="{{ route('artist.profile.update') }}" enctype="multipart/form-data" class="artist-form">
                @csrf
                <label class="artist-field">
                    <span>Name</span>
                    <input name="name" value="{{ old('name', $artist->name) }}" required>
                </label>
                <label class="artist-field">
                    <span>Email</span>
                    <input value="{{ $artist->email }}" disabled>
                    <small>Email is used for login. Admin can change it if needed.</small>
                </label>
                <label class="artist-field">
                    <span>Phone</span>
                    <input name="phone" value="{{ old('phone', $artist->phone) }}">
                </label>
                <label class="artist-field">
                    <span>Nationality</span>
                    <input name="nationality" value="{{ old('nationality', $artist->nationality) }}">
                </label>
                <label class="artist-field">
                    <span>Birth place</span>
                    <input name="birth_place" value="{{ old('birth_place', $artist->birth_place) }}">
                </label>
                <label class="artist-field">
                    <span>Profile picture</span>
                    <input type="file" name="picture" accept="image/*">
                </label>
                <label class="artist-field md:col-span-2">
                    <span>Biography</span>
                    <textarea name="biography">{{ old('biography', $artist->biography) }}</textarea>
                </label>
                <button class="artist-submit md:col-span-2">Save profile</button>
            </form>
        </section>
    </main>
</x-artstory.layout>
