<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Artist;
use App\Support\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class ArtistApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $siteTitle;

    public ?string $siteLogoUrl;

    public string $loginUrl;

    public string $supportEmail;

    public string $subjectLine;

    public ?string $htmlBody = null;

    public ?string $textBody = null;

    public function __construct(
        public Artist $artist,
        public ?string $temporaryPassword,
    ) {
        $siteSettings = SiteSettings::current();
        $this->siteTitle = $siteSettings?->site_title ?: 'ART Story';
        $this->siteLogoUrl = $this->absoluteUrl($siteSettings?->loginLogoUrl() ?: $siteSettings?->adminLogoUrl());
        $this->supportEmail = $siteSettings?->contact_email ?: config('mail.from.address');
        $this->loginUrl = route('artist.login');
        $this->subjectLine = $this->siteTitle . ' artist account approved';

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
            view: $this->htmlBody ? 'emails.artstory-dynamic' : 'emails.artist-approved',
            text: $this->textBody ? 'emails.dynamic-text' : null,
            with: [
                'artist' => $this->artist,
                'temporaryPassword' => $this->temporaryPassword,
                'siteTitle' => $this->siteTitle,
                'siteLogoUrl' => $this->siteLogoUrl,
                'loginUrl' => $this->loginUrl,
                'supportEmail' => $this->supportEmail,
                'htmlBody' => $this->htmlBody,
                'textBody' => $this->textBody,
            ],
        );
    }

    private function renderDynamicTemplate(): void
    {
        $template = EmailTemplate::activeTemplate('artist_account_approved');

        if (! $template) {
            return;
        }

        $variables = [
            'artist' => $this->artist,
            'artistName' => $this->artist->name,
            'artistEmail' => $this->artist->email,
            'temporaryPassword' => $this->temporaryPassword,
            'loginUrl' => $this->loginUrl,
            'supportEmail' => $this->supportEmail,
            'siteTitle' => $this->siteTitle,
            'siteLogoUrl' => $this->siteLogoUrl,
        ];

        $this->subjectLine = trim(Blade::render($template->subject, $variables)) ?: $this->subjectLine;
        $this->htmlBody = Blade::render($template->body_html, $variables);
        $this->textBody = filled($template->body_text)
            ? Blade::render($template->body_text, $variables)
            : trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $this->htmlBody)));
    }

    private function absoluteUrl(?string $url): ?string
    {
        return $url ? url($url) : null;
    }
}
