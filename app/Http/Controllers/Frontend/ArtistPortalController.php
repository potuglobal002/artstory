<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkMedium;
use App\Models\ArtworkSize;
use App\Models\ArtworkStyle;
use App\Models\ArtworkSubjectStyle;
use App\Support\AdminNotifier;
use App\Support\ArtworkImageOptimizer;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArtistPortalController extends Controller
{
    public function register(): View
    {
        return view('artstory.artist-portal.register', [
            'siteSettings' => SiteSettings::current(),
        ]);
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:artists,email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string', 'max:3000'],
            'picture' => ['nullable', 'image', 'max:4096'],
        ]);

        $artist = new Artist([
            'name' => $data['name'],
            'email' => str($data['email'])->trim()->lower()->toString(),
            'phone' => $data['phone'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'birth_place' => $data['birth_place'] ?? null,
            'biography' => $data['biography'] ?? null,
            'approval_status' => 'pending',
            'is_active' => false,
        ]);

        if ($request->hasFile('picture')) {
            $artist->picture_path = $request->file('picture')->store('artists', 'public');
        }

        $artist->save();

        AdminNotifier::artistRegistered($artist);

        return redirect()
            ->route('artist.register')
            ->with('status', 'Registration submitted. ART Story will review your profile and email login access after approval.');
    }

    public function login(): View
    {
        return view('artstory.artist-portal.login', [
            'siteSettings' => SiteSettings::current(),
        ]);
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'email' => str($data['email'])->trim()->lower()->toString(),
            'password' => $data['password'],
        ];

        if (! Auth::guard('artist')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These login details do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        if (! $this->currentArtist()) {
            Auth::guard('artist')->logout();

            return back()->withErrors(['email' => 'This account is not linked to an approved artist profile.'])->onlyInput('email');
        }

        return redirect()->route('artist.dashboard');
    }

    public function dashboard(): View|RedirectResponse
    {
        $artist = $this->currentArtist();

        if (! $artist) {
            return redirect()->route('artist.login');
        }

        return view('artstory.artist-portal.dashboard', [
            'siteSettings' => SiteSettings::current(),
            'artist' => $artist,
            'artworks' => $artist->artworks()->with(['style', 'medium', 'size'])->latest()->get(),
        ]);
    }

    public function profile(): View|RedirectResponse
    {
        $artist = $this->currentArtist();

        if (! $artist) {
            return redirect()->route('artist.login');
        }

        return view('artstory.artist-portal.profile', [
            'siteSettings' => SiteSettings::current(),
            'artist' => $artist,
        ]);
    }

    public function createArtwork(Request $request): View|RedirectResponse
    {
        $artist = $this->currentArtist();

        if (! $artist) {
            return redirect()->route('artist.login');
        }

        return view('artstory.artist-portal.upload-artwork', [
            'siteSettings' => SiteSettings::current(),
            'artist' => $artist,
            'submissionToken' => $this->newArtworkSubmissionToken($request),
            'styles' => ArtworkStyle::query()->where('is_active', true)->orderBy('name')->get(),
            'subjectStyles' => ArtworkSubjectStyle::query()->where('is_active', true)->orderBy('name')->get(),
            'mediums' => ArtworkMedium::query()->where('is_active', true)->orderBy('name')->get(),
            'sizes' => ArtworkSize::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeArtwork(Request $request): RedirectResponse
    {
        $artist = $this->currentArtist();

        if (! $artist) {
            return redirect()->route('artist.login');
        }

        $submissionToken = (string) $request->input('submission_token');
        $sessionTokens = $request->session()->get('artist_artwork_submission_tokens', []);

        if (! $submissionToken || ! in_array($submissionToken, $sessionTokens, true)) {
            return redirect()
                ->route('artist.dashboard')
                ->with('status', 'This artwork submission was already received or expired.');
        }

        $request->session()->put(
            'artist_artwork_submission_tokens',
            array_values(array_diff($sessionTokens, [$submissionToken])),
        );

        $duplicateKey = $this->artworkDuplicateKey($artist, $request);

        if (! Cache::add($duplicateKey, true, now()->addMinutes(5))) {
            return redirect()
                ->route('artist.dashboard')
                ->with('status', 'This artwork submission is already in review.');
        }

        foreach ([
            'artwork_style_id',
            'artwork_subject_style_id',
            'artwork_medium_id',
            'artwork_size_id',
        ] as $field) {
            if ($request->input($field) === '__custom') {
                $request->merge([$field => null]);
            }
        }

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'artwork_style_id' => ['nullable', Rule::exists('artwork_styles', 'id')],
            'artwork_subject_style_id' => ['nullable', Rule::exists('artwork_subject_styles', 'id')],
            'artwork_medium_id' => ['nullable', Rule::exists('artwork_mediums', 'id')],
            'artwork_size_id' => ['nullable', Rule::exists('artwork_sizes', 'id')],
            'custom_style' => ['nullable', 'string', 'max:255'],
            'custom_subject_style' => ['nullable', 'string', 'max:255'],
            'custom_medium' => ['nullable', 'string', 'max:255'],
            'custom_size' => ['nullable', 'string', 'max:255'],
            'submission_token' => ['required', 'string'],
            'year' => ['nullable', 'integer', 'min:1000', 'max:' . now()->addYear()->year],
            'canvas_count' => ['required', 'integer', 'min:1', 'max:20'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'usd_price' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:3000'],
            'image' => ['required', 'image', 'max:51200'],
            'gallery_images' => ['nullable', 'array', 'max:12'],
            'gallery_images.*' => ['image', 'max:51200'],
        ]);

        $data['artist_style_name'] = $this->temporaryClassification($data['custom_style'] ?? null);
        $data['artist_subject_style_name'] = $this->temporaryClassification($data['custom_subject_style'] ?? null);
        $data['artist_medium_name'] = $this->temporaryClassification($data['custom_medium'] ?? null);
        $data['artist_size_name'] = $this->temporaryClassification($data['custom_size'] ?? null);
        $data['artist_id'] = $artist->id;
        $data['image_path'] = ArtworkImageOptimizer::store($request->file('image'));
        $data['gallery_images'] = collect($request->file('gallery_images', []))
            ->map(fn ($file): string => ArtworkImageOptimizer::store($file))
            ->values()
            ->all();
        $data['status'] = 'available';
        $data['is_active'] = false;
        $data['submitted_by_artist'] = true;
        unset($data['image'], $data['custom_style'], $data['custom_subject_style'], $data['custom_medium'], $data['custom_size']);
        unset($data['submission_token']);

        $artwork = Artwork::query()->create($data);

        AdminNotifier::artworkSubmitted($artwork->load('artist'));

        return redirect()
            ->route('artist.dashboard')
            ->with('status', 'Artwork submitted. ART Story will review it before publishing.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $artist = $this->currentArtist();

        if (! $artist) {
            return redirect()->route('artist.login');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string', 'max:3000'],
            'picture' => ['nullable', 'image', 'max:4096'],
        ]);

        $artist->fill($data);

        if ($request->hasFile('picture')) {
            $artist->picture_path = $request->file('picture')->store('artists', 'public');
        }

        $artist->save();

        if ($artist->user) {
            $artist->user->forceFill(['name' => $artist->name])->save();
        }

        return redirect()
            ->route('artist.profile')
            ->with('status', 'Profile updated successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('artist')->logout();

        return redirect()->route('home');
    }

    private function currentArtist(): ?Artist
    {
        $user = Auth::guard('artist')->user();

        if (! $user) {
            return null;
        }

        return Artist::query()
            ->where('user_id', $user->id)
            ->where('approval_status', 'approved')
            ->where('is_active', true)
            ->first();
    }

    private function temporaryClassification(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function newArtworkSubmissionToken(Request $request): string
    {
        $token = (string) Str::uuid();
        $tokens = $request->session()->get('artist_artwork_submission_tokens', []);
        $tokens[] = $token;

        $request->session()->put('artist_artwork_submission_tokens', array_slice($tokens, -5));

        return $token;
    }

    private function artworkDuplicateKey(Artist $artist, Request $request): string
    {
        $file = $request->file('image');

        return 'artist-artwork-upload:' . sha1(implode('|', [
            $artist->id,
            trim((string) $request->input('title', 'Untitled')),
            $file?->getClientOriginalName(),
            $file?->getSize(),
        ]));
    }
}
