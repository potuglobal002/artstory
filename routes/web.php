<?php

use App\Http\Controllers\Frontend\ArtistController;
use App\Http\Controllers\Frontend\ArtistPortalController;
use App\Http\Controllers\Frontend\ArtworkController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\VirtualGalleryController;
use App\Models\Artist;
use App\Models\Artwork;
use App\Support\LandingPageSettings;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $landingPage = LandingPageSettings::current();
    $featuredArtworkIds = collect($landingPage?->featured_artwork_ids ?: [])->filter()->values();
    $featuredArtistIds = collect($landingPage?->featured_artist_ids ?: [])->filter()->values();

    $featuredArtworks = collect();
    $featuredArtists = collect();

    if (Schema::hasTable('artworks')) {
        $artworkQuery = Artwork::query()
            ->with(['artist', 'style', 'subjectStyle', 'medium', 'size'])
            ->where('is_active', true)
            ->whereHas('artist', fn ($query) => $query->where('is_active', true));

        $featuredArtworks = $featuredArtworkIds->isNotEmpty()
            ? $artworkQuery->whereIn('id', $featuredArtworkIds)->get()->sortBy(fn (Artwork $artwork) => $featuredArtworkIds->search($artwork->id))->values()
            : $artworkQuery->inRandomOrder()->limit(12)->get();
    }

    if (Schema::hasTable('artists')) {
        $artistQuery = Artist::query()
            ->withCount(['artworks' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true);

        $featuredArtists = $featuredArtistIds->isNotEmpty()
            ? $artistQuery->whereIn('id', $featuredArtistIds)->get()->sortBy(fn (Artist $artist) => $featuredArtistIds->search($artist->id))->values()
            : $artistQuery->orderBy('name')->limit(4)->get();
    }

    return view('welcome', [
        'siteSettings' => SiteSettings::current(),
        'landingPage' => $landingPage,
        'featuredArtworks' => $featuredArtworks,
        'featuredArtists' => $featuredArtists,
    ]);
})->name('home');

Route::get('/artworks', [ArtworkController::class, 'index'])->name('artworks.index');
Route::middleware('feature.enabled:virtual_gallery')->group(function (): void {
    Route::get('/virtual-galleries', [VirtualGalleryController::class, 'index'])->name('virtual-galleries.index');
    Route::get('/virtual-galleries/{gallery:slug}', [VirtualGalleryController::class, 'show'])->name('virtual-galleries.show');
});
Route::get('/artwork-code/{artwork:artwork_code}', [ArtworkController::class, 'scan'])->name('artworks.scan');
Route::get('/artwork-code/{artwork:artwork_code}/qr.svg', [ArtworkController::class, 'qr'])->name('artworks.qr');
Route::get('/artwork-code/{artwork:artwork_code}/barcode.svg', [ArtworkController::class, 'barcode'])->name('artworks.barcode');
Route::get('/artwork-qr-print', [ArtworkController::class, 'printQr'])->name('artworks.qr.print');
Route::get('/artworks/{artwork}', [ArtworkController::class, 'show'])->name('artworks.show');
Route::post('/artworks/{artwork}/inquiries', [ArtworkController::class, 'storeInquiry'])->name('artworks.inquiries.store');
Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
Route::get('/artists/{artist}', [ArtistController::class, 'show'])->name('artists.show');
Route::middleware('feature.enabled:artist_login')->group(function (): void {
    Route::get('/artist/register', [ArtistPortalController::class, 'register'])->name('artist.register');
    Route::post('/artist/register', [ArtistPortalController::class, 'storeRegistration'])->name('artist.register.store');
    Route::get('/artist/login', [ArtistPortalController::class, 'login'])->name('artist.login');
    Route::get('/login', fn () => redirect()->route('artist.login'))->name('login');
    Route::post('/artist/login', [ArtistPortalController::class, 'authenticate'])->name('artist.login.store');
    Route::post('/artist/logout', [ArtistPortalController::class, 'logout'])->name('artist.logout');
    Route::get('/artist/dashboard', [ArtistPortalController::class, 'dashboard'])->name('artist.dashboard');
    Route::get('/artist/profile', [ArtistPortalController::class, 'profile'])->name('artist.profile');
    Route::post('/artist/profile', [ArtistPortalController::class, 'updateProfile'])->name('artist.profile.update');
    Route::get('/artist/artworks', fn () => redirect()->route('artist.dashboard'))->name('artist.artworks.index');
    Route::get('/artist/artworks/create', [ArtistPortalController::class, 'createArtwork'])->name('artist.artworks.create');
    Route::post('/artist/artworks', [ArtistPortalController::class, 'storeArtwork'])->name('artist.artworks.store');
});
Route::middleware('feature.enabled:exhibitions')->group(function (): void {
    Route::get('/exhibitions', [PageController::class, 'exhibitions'])->name('exhibitions.index');
    Route::get('/exhibitions/{exhibition:slug}', [PageController::class, 'exhibition'])->name('exhibitions.show');
});
Route::middleware('feature.enabled:events_pr')->group(function (): void {
    Route::get('/upcoming-events', [PageController::class, 'events'])->name('events.index');
    Route::get('/upcoming-events/{event:slug}', [PageController::class, 'event'])->name('events.show');
});
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContactMessage'])->name('contact.store');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-conditions', [PageController::class, 'termsConditions'])->name('terms-conditions');
