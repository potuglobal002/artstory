<?php

namespace App\Mail;

use App\Models\Artwork;
use App\Models\EmailTemplate;
use App\Support\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class ArtworkPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $siteTitle;

    public ?string $siteLogoUrl;

    public string $supportEmail;

    public string $dashboardUrl;

    public string $subjectLine;

    public ?string $htmlBody = null;

    public ?string $textBody = null;

    public function __construct(public Artwork $artwork)
    {
        $this->artwork->loadMissing(['artist', 'style', 'subjectStyle', 'medium', 'size']);

        $siteSettings = SiteSettings::current();
        $this->siteTitle = $siteSettings?->site_title ?: 'ART Story';
        $this->siteLogoUrl = $this->absoluteUrl($siteSettings?->loginLogoUrl() ?: $siteSettings?->adminLogoUrl());
        $this->supportEmail = $siteSettings?->contact_email ?: config('mail.from.address');
        $this->dashboardUrl = route('artist.dashboard');
        $this->subjectLine = $this->siteTitle . ' artwork approved: ' . $this->artwork->title;

        $this->renderDynamicTemplate();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
            replyTo: [$this->supportEmail],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->htmlBody ? 'emails.artstory-dynamic' : 'emails.artwork-published',
            text: $this->textBody ? 'emails.dynamic-text' : null,
            with: [
                'artwork' => $this->artwork,
                'artist' => $this->artwork->artist,
                'siteTitle' => $this->siteTitle,
                'siteLogoUrl' => $this->siteLogoUrl,
                'supportEmail' => $this->supportEmail,
                'dashboardUrl' => $this->dashboardUrl,
                'htmlBody' => $this->htmlBody,
                'textBody' => $this->textBody,
            ],
        );
    }

    private function renderDynamicTemplate(): void
    {
        $template = EmailTemplate::activeTemplate('artist_artwork_published');

        if (! $template) {
            return;
        }

        $variables = $this->templateVariables();

        $this->subjectLine = trim(Blade::render($template->subject, $variables)) ?: $this->subjectLine;
        $this->htmlBody = Blade::render($template->body_html, $variables);
        $this->textBody = filled($template->body_text)
            ? Blade::render($template->body_text, $variables)
            : trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $this->htmlBody)));
    }

    private function templateVariables(): array
    {
        return [
            'artwork' => $this->artwork,
            'artist' => $this->artwork->artist,
            'artistName' => $this->artwork->artist?->name ?: 'Artist',
            'artworkTitle' => $this->artwork->title,
            'artworkYear' => $this->artwork->year,
            'artworkStyle' => $this->artwork->style?->name,
            'artworkSubjectStyle' => $this->artwork->subjectStyle?->name,
            'artworkMedium' => $this->artwork->medium?->name,
            'artworkSize' => $this->artwork->size?->label(),
            'dashboardUrl' => $this->dashboardUrl,
            'supportEmail' => $this->supportEmail,
            'siteTitle' => $this->siteTitle,
            'siteLogoUrl' => $this->siteLogoUrl,
        ];
    }

    private function absoluteUrl(?string $url): ?string
    {
        return $url ? url($url) : null;
    }
}
