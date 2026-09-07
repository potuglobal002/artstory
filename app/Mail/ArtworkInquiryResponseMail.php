<?php

namespace App\Mail;

use App\Models\ArtworkInquiry;
use App\Models\EmailTemplate;
use App\Support\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class ArtworkInquiryResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public ?string $htmlBody = null;
    public ?string $textBody = null;
    public string $siteTitle;
    public ?string $siteLogoUrl;
    public string $supportEmail;
    public string $supportPhone;

    public function __construct(public ArtworkInquiry $inquiry)
    {
        $this->inquiry->loadMissing(['artwork.artist', 'artwork.medium', 'artwork.size', 'artwork.style', 'artwork.subjectStyle']);
        $settings = SiteSettings::current();
        $this->siteTitle = $settings->site_title ?: 'ART Story';
        $this->siteLogoUrl = $settings->loginLogoUrl() ?: $settings->adminLogoUrl();
        $this->supportEmail = $settings->contact_email ?: config('mail.from.address');
        $this->supportPhone = $settings->contact_phone ?: '';
        $this->subjectLine = 'Thank you for your interest in ' . ($inquiry->artwork_title ?: 'this artwork');
        $this->renderDynamicTemplate();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine, replyTo: [$this->supportEmail]);
    }

    public function content(): Content
    {
        return new Content(
            view: $this->htmlBody ? 'emails.artstory-dynamic' : 'emails.artwork-inquiry-response',
            text: $this->textBody ? 'emails.dynamic-text' : null,
            with: ['inquiry' => $this->inquiry, 'siteTitle' => $this->siteTitle, 'siteLogoUrl' => $this->siteLogoUrl, 'supportEmail' => $this->supportEmail, 'supportPhone' => $this->supportPhone, 'htmlBody' => $this->htmlBody, 'textBody' => $this->textBody],
        );
    }

    private function renderDynamicTemplate(): void
    {
        $template = EmailTemplate::activeTemplate('artwork_inquiry_response');
        if (! $template) return;

        $variables = [
            'inquiry' => $this->inquiry,
            'artwork' => $this->inquiry->artwork,
            'customerName' => $this->inquiry->name,
            'customerEmail' => $this->inquiry->email,
            'customerWhatsapp' => $this->inquiry->whatsapp,
            'inquiryMessage' => $this->inquiry->message,
            'artworkTitle' => $this->inquiry->artwork_title ?: 'Untitled artwork',
            'artworkCode' => $this->inquiry->artwork_code,
            'artistName' => $this->inquiry->artist_name,
            'artworkUrl' => $this->inquiry->artwork ? route('artworks.show', $this->inquiry->artwork) : url('/artworks'),
            'artworkImageUrl' => $this->inquiry->artwork?->imageUrl() ? url($this->inquiry->artwork->imageUrl()) : null,
            'supportEmail' => $this->supportEmail, 'supportPhone' => $this->supportPhone,
            'siteTitle' => $this->siteTitle, 'siteLogoUrl' => $this->siteLogoUrl ? url($this->siteLogoUrl) : null,
        ];

        $this->subjectLine = trim(Blade::render($template->subject, $variables)) ?: $this->subjectLine;
        $this->htmlBody = Blade::render($template->body_html, $variables) . $this->artworkDetailsHtml();
        $this->textBody = (filled($template->body_text) ? Blade::render($template->body_text, $variables) : trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $this->htmlBody)))) . $this->artworkDetailsText();
    }

    private function artworkDetailsHtml(): string
    {
        $artwork = $this->inquiry->artwork;
        if (! $artwork) {
            return '';
        }

        $imageUrl = $artwork->imageUrl();
        $imageUrl = $imageUrl ? url($imageUrl) : null;
        $details = [
            'Artist' => $this->inquiry->artist_name ?: $artwork->artist?->name,
            'Medium' => $artwork->medium?->name ?: 'Not specified',
            'Dimensions' => $artwork->size?->label() ?: 'Not specified',
            'Year' => $artwork->year ?: 'Not specified',
            'Canvases' => $artwork->canvas_count ?: 1,
            'Artwork code' => $artwork->artwork_code ?: $this->inquiry->artwork_code,
        ];

        $rows = collect($details)->filter(fn ($value): bool => filled($value))
            ->map(fn ($value, $label): string => '<tr><td style="padding:4px 0;color:#64748b;font-weight:700;width:110px;">' . e($label) . '</td><td style="padding:4px 0;color:#334155;">' . e((string) $value) . '</td></tr>')
            ->implode('');

        return '<div style="margin-top:28px;padding-top:22px;border-top:1px solid #e5e7eb;">'
            . '<h3 style="margin:0 0 14px;color:#111827;font-size:17px;">Artwork details</h3>'
            . ($imageUrl ? '<img src="' . e($imageUrl) . '" alt="' . e($artwork->title ?: 'Artwork') . '" style="display:block;width:100%;max-width:360px;height:auto;max-height:360px;object-fit:contain;background:#f8fafc;margin:0 0 18px;">' : '')
            . '<table role="presentation" cellspacing="0" cellpadding="0" style="width:100%;font-size:14px;line-height:1.45;"><tr><td style="padding:4px 0;color:#64748b;font-weight:700;width:110px;">Title</td><td style="padding:4px 0;color:#334155;font-weight:700;">' . e($artwork->title ?: $this->inquiry->artwork_title) . '</td></tr>' . $rows . '</table>'
            . '</div>';
    }

    private function artworkDetailsText(): string
    {
        $artwork = $this->inquiry->artwork;
        if (! $artwork) {
            return '';
        }

        return "\n\nARTWORK DETAILS\n"
            . 'Title: ' . ($artwork->title ?: $this->inquiry->artwork_title) . "\n"
            . 'Artist: ' . ($this->inquiry->artist_name ?: $artwork->artist?->name ?: 'Not specified') . "\n"
            . 'Medium: ' . ($artwork->medium?->name ?: 'Not specified') . "\n"
            . 'Dimensions: ' . ($artwork->size?->label() ?: 'Not specified') . "\n"
            . 'Year: ' . ($artwork->year ?: 'Not specified') . "\n"
            . 'Canvases: ' . ($artwork->canvas_count ?: 1) . "\n"
            . 'Artwork code: ' . ($artwork->artwork_code ?: $this->inquiry->artwork_code ?: 'Not specified');
    }
}
