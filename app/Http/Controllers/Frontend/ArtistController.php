<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artwork;
use App\Support\SiteSettings;

class ArtistController extends Controller
{
    public function index()
    {
        $artists = Artist::query()
            ->withCount(['artworks' => fn ($query) => $query->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->paginate(12);

        return view('artstory.artists.index', [
            'siteSettings' => SiteSettings::current(),
            'artists' => $artists,
        ]);
    }

    public function show(Artist $artist)
    {
        abort_unless($artist->is_active, 404);

        $artworks = Artwork::query()
            ->with(['style', 'size'])
            ->where('artist_id', $artist->getKey())
            ->where('is_active', true)
            ->latest()
            ->paginate(9);

        return view('artstory.artists.show', [
            'siteSettings' => SiteSettings::current(),
            'artist' => $artist,
            'artworks' => $artworks,
        ]);
    }
}
