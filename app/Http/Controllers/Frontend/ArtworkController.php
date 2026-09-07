<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkInquiry;
use App\Support\AdminNotifier;
use App\Models\ArtworkMedium;
use App\Models\ArtworkSize;
use App\Models\ArtworkStyle;
use App\Models\ArtworkSubjectStyle;
use App\Support\ArtworkQrCode;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ArtworkController extends Controller
{
    public function index(Request $request)
    {
        $currency = strtoupper($request->string('currency')->toString()) === 'USD' ? 'USD' : 'BDT';
        $priceExpression = $currency === 'USD'
            ? 'COALESCE(usd_selling_price, usd_price)'
            : 'COALESCE(selling_price, price)';

        $priceMax = (int) Artwork::query()
            ->where('is_active', true)
            ->whereRaw($priceExpression . ' IS NOT NULL')
            ->selectRaw('MAX(' . $priceExpression . ') as max_price')
            ->value('max_price');

        $priceMax = max(10000, (int) ceil($priceMax / 1000) * 1000);
        $statusCounts = Artwork::query()
            ->where('is_active', true)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $artworks = Artwork::query()
            ->with(['artist', 'style', 'subjectStyle', 'medium', 'size'])
            ->where('is_active', true)
            ->whereHas('artist', fn ($query) => $query->where('is_active', true))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q')->toString();

                $query->where(function ($query) use ($term) {
                    $query
                        ->where('title', 'like', "%{$term}%")
                        ->orWhere('note', 'like', "%{$term}%")
                        ->orWhereHas('artist', fn ($artistQuery) => $artistQuery->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('artist'), fn ($query) => $query->where('artist_id', $request->integer('artist')))
            ->when($request->filled('style'), fn ($query) => $query->where('artwork_style_id', $request->integer('style')))
            ->when($request->filled('subject_style'), fn ($query) => $query->where('artwork_subject_style_id', $request->integer('subject_style')))
            ->when($request->filled('medium'), fn ($query) => $query->where('artwork_medium_id', $request->integer('medium')))
            ->when($request->filled('size'), fn ($query) => $query->where('artwork_size_id', $request->integer('size')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('price_min'), fn ($query) => $query->whereRaw($priceExpression . ' >= ?', [$request->input('price_min')]))
            ->when($request->filled('price_max'), fn ($query) => $query->whereRaw($priceExpression . ' <= ?', [$request->input('price_max')]))
            ->when(
                $request->string('sort')->toString() === 'price_low',
                fn ($query) => $query->orderByRaw($priceExpression . ' IS NULL')->orderByRaw($priceExpression . ' ASC'),
                fn ($query) => $query->when(
                    $request->string('sort')->toString() === 'price_high',
                    fn ($query) => $query->orderByRaw($priceExpression . ' IS NULL')->orderByRaw($priceExpression . ' DESC'),
                    fn ($query) => $query->latest()
                )
            )
            ->paginate(9)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('artstory.artworks.partials.cards', compact('artworks', 'currency'))->render(),
                'next_page_url' => $artworks->nextPageUrl(),
                'shown' => $artworks->lastItem() ?? 0,
                'total' => $artworks->total(),
            ]);
        }

        return view('artstory.artworks.index', [
            'siteSettings' => SiteSettings::current(),
            'artworks' => $artworks,
            'artists' => Artist::query()
                ->withCount(['artworks' => fn ($query) => $query->where('is_active', true)])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'styles' => ArtworkStyle::query()
                ->withCount(['artworks' => fn ($query) => $query->where('is_active', true)])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'sizes' => ArtworkSize::query()->where('is_active', true)->orderBy('name')->get(),
            'subjectStyles' => ArtworkSubjectStyle::query()->where('is_active', true)->orderBy('name')->get(),
            'mediums' => ArtworkMedium::query()->where('is_active', true)->orderBy('name')->get(),
            'priceMax' => $priceMax,
            'statusCounts' => $statusCounts,
            'currency' => $currency,
        ]);
    }

    public function show(Artwork $artwork)
    {
        abort_unless($artwork->is_active && $artwork->artist?->is_active, 404);

        $artwork->load(['artist', 'style', 'subjectStyle', 'medium', 'size']);

        $relatedArtworks = Artwork::query()
            ->with(['artist', 'style', 'subjectStyle', 'medium', 'size'])
            ->where('is_active', true)
            ->whereKeyNot($artwork->getKey())
            ->where('artist_id', $artwork->artist_id)
            ->latest()
            ->limit(3)
            ->get();

        return view('artstory.artworks.show', [
            'siteSettings' => SiteSettings::current(),
            'artwork' => $artwork,
            'relatedArtworks' => $relatedArtworks,
            'currency' => strtoupper(request()->string('currency')->toString()) === 'USD' ? 'USD' : 'BDT',
        ]);
    }

    public function storeInquiry(Request $request, Artwork $artwork)
    {
        abort_unless($artwork->is_active && $artwork->artist?->is_active, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'whatsapp' => ['required', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $inquiry = ArtworkInquiry::create([
            ...$data,
            'artwork_id' => $artwork->id,
            'artwork_code' => $artwork->artwork_code,
            'artwork_title' => $artwork->title ?: 'Untitled artwork',
            'artist_name' => $artwork->artist?->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        AdminNotifier::artworkInquiryReceived($inquiry);

        $settings = SiteSettings::current();
        $template = $settings->whatsapp_inquiry_template ?: "Hello ART Story, I am interested in {{ artwork_title }} by {{ artist_name }}.\n\nName: {{ name }}\nWhatsApp: {{ whatsapp }}\nEmail: {{ email }}\nMessage: {{ message }}\n\nArtwork link: {{ artwork_url }}";
        $replacements = [
            '{{ artwork_title }}' => $inquiry->artwork_title,
            '{{ artist_name }}' => $inquiry->artist_name,
            '{{ artwork_code }}' => $inquiry->artwork_code,
            '{{ name }}' => $inquiry->name,
            '{{ whatsapp }}' => $inquiry->whatsapp,
            '{{ email }}' => $inquiry->email ?: '-',
            '{{ message }}' => $inquiry->message,
            '{{ artwork_url }}' => route('artworks.show', $artwork),
        ];
        $text = strtr($template, $replacements);
        $phone = preg_replace('/\D+/', '', $settings->whatsapp_phone ?: $settings->contact_phone ?: '');

        return response()->json([
            'message' => 'Inquiry saved successfully.',
            'whatsapp_url' => $phone ? 'https://wa.me/' . $phone . '?text=' . rawurlencode($text) : null,
        ]);
    }

    public function scan(Artwork $artwork)
    {
        return $this->show($artwork);
    }

    public function qr(Artwork $artwork): Response
    {
        abort_unless(filled($artwork->artwork_code), 404);

        return response(ArtworkQrCode::svg($artwork->scanUrl()))
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    public function printQr(Request $request)
    {
        $ids = Str::of($request->string('artworks')->toString())
            ->explode(',')
            ->map(fn (string $id): int => (int) $id)
            ->filter()
            ->values();

        $artworks = Artwork::query()
            ->with(['artist', 'medium', 'size'])
            ->whereIn('id', $ids)
            ->orderBy('artist_id')
            ->orderBy('artwork_code')
            ->get();

        abort_if($artworks->isEmpty(), 404);

        return view('artstory.artworks.qr-print', [
            'siteSettings' => SiteSettings::current(),
            'artworks' => $artworks,
        ]);
    }

    public function barcode(Artwork $artwork): Response
    {
        return $this->qr($artwork);
    }
}
