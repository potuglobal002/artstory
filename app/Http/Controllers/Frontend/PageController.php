<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\Exhibition;
use App\Support\AdminNotifier;
use App\Support\LandingPageSettings;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('artstory.pages.about', [
            'siteSettings' => SiteSettings::current(),
            'artistCount' => Artist::query()->where('is_active', true)->count(),
            'artworkCount' => Artwork::query()->where('is_active', true)->count(),
        ]);
    }

    public function exhibition(Exhibition $exhibition)
    {
        abort_unless($exhibition->is_active, 404);
        $exhibition->load(['artworks.artist']);

        return view('artstory.pages.exhibition', ['siteSettings' => SiteSettings::current(), 'exhibition' => $exhibition]);
    }

    public function contact()
    {
        return view('artstory.pages.contact', [
            'siteSettings' => SiteSettings::current(),
        ]);
    }

    public function privacyPolicy()
    {
        return $this->legalPage(
            'privacy_policy_title',
            'privacy_policy_content',
            'Privacy Policy',
            '<p>ART Story respects your privacy and handles personal information responsibly.</p>',
        );
    }

    public function termsConditions()
    {
        return $this->legalPage(
            'terms_conditions_title',
            'terms_conditions_content',
            'Terms & Conditions',
            '<p>By using ART Story, you agree to use the website and its content responsibly.</p>',
        );
    }

    public function storeContactMessage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $message = ContactMessage::create([
            ...$data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        AdminNotifier::contactMessageReceived($message);

        return back()->with('contact_message_sent', true);
    }

    public function exhibitions()
    {
        $landingPage = LandingPageSettings::current();

        return view('artstory.pages.exhibitions', [
            'siteSettings' => SiteSettings::current(),
            'landingPage' => $landingPage,
            'featuredArtworks' => Artwork::query()
                ->with(['artist', 'style', 'medium', 'size'])
                ->where('is_active', true)
                ->whereHas('artist', fn ($query) => $query->where('is_active', true))
                ->latest()
                ->limit(6)
                ->get(),
            'artistCount' => Artist::query()->where('is_active', true)->count(),
            'artworkCount' => Artwork::query()->where('is_active', true)->count(),
            'exhibitions' => Exhibition::query()->withCount('artworks')->where('is_active', true)->orderBy('sort_order')->latest('start_date')->get(),
        ]);
    }

    public function events()
    {
        $landingPage = LandingPageSettings::current();

        return view('artstory.pages.events', [
            'siteSettings' => SiteSettings::current(),
            'landingPage' => $landingPage,
            'featuredArtworks' => Artwork::query()
                ->with(['artist', 'style', 'medium', 'size'])
                ->where('is_active', true)
                ->whereHas('artist', fn ($query) => $query->where('is_active', true))
                ->inRandomOrder()
                ->limit(4)
                ->get(),
            'events' => Event::query()->where('is_active', true)->orderBy('sort_order')->latest('start_date')->get(),
        ]);
    }

    public function event(Event $event)
    {
        abort_unless($event->is_active, 404);

        return view('artstory.pages.event', ['siteSettings' => SiteSettings::current(), 'event' => $event]);
    }

    private function legalPage(string $titleField, string $contentField, string $defaultTitle, string $defaultContent)
    {
        $siteSettings = SiteSettings::current();

        return view('artstory.pages.legal', [
            'siteSettings' => $siteSettings,
            'title' => $siteSettings?->{$titleField} ?: $defaultTitle,
            'content' => $siteSettings?->{$contentField} ?: $defaultContent,
        ]);
    }
}
