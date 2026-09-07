<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\ArtworkSale;
use App\Support\ArtworkSaleInvoicePdf;
use App\Support\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class ArtworkSaleInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $supportEmail;

    public string $supportPhone;

    public string $siteTitle;

    public ?string $siteLogoUrl;

    public string $subjectLine;

    public ?string $htmlBody = null;

    public ?string $textBody = null;

    public function __construct(public ArtworkSale $sale)
    {
        $this->sale->loadMissing(['artwork.style', 'artwork.subjectStyle', 'artwork.medium', 'artwork.size', 'artist', 'buyer', 'paymentMethod']);

        $siteSettings = SiteSettings::current();
        $this->siteTitle = $siteSettings?->site_title ?: 'ART Story';
        $this->siteLogoUrl = $this->absoluteUrl($siteSettings?->loginLogoUrl() ?: $siteSettings?->adminLogoUrl());
        $this->supportEmail = $siteSettings?->contact_email ?: config('mail.from.address');
        $this->supportPhone = $siteSettings?->contact_phone ?: '';
        $this->subjectLine = $this->siteTitle . ' invoice ' . ($this->sale->invoice_number ?: '');

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
            view: $this->htmlBody ? 'emails.artwork-sale-invoice-dynamic' : 'emails.artwork-sale-invoice',
            text: $this->textBody ? 'emails.dynamic-text' : null,
            with: [
                'sale' => $this->sale,
                'supportEmail' => $this->supportEmail,
                'supportPhone' => $this->supportPhone,
                'siteTitle' => $this->siteTitle,
                'siteLogoUrl' => $this->siteLogoUrl,
                'htmlBody' => $this->htmlBody,
                'textBody' => $this->textBody,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath(ArtworkSaleInvoicePdf::file($this->sale))
                ->as(($this->sale->invoice_number ?: 'artwork-invoice') . '.pdf')
                ->withMime('application/pdf'),
        ];
    }

    private function renderDynamicTemplate(): void
    {
        $template = EmailTemplate::activeTemplate('artwork_sale_invoice');

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

    /**
     * @return array<string, mixed>
     */
    private function templateVariables(): array
    {
        $lineItems = $this->sale->displayLineItems();
        $subtotal = $this->sale->subtotal();
        $taxAmount = (float) ($this->sale->tax_amount ?? 0);
        $total = $this->sale->totalPaid();

        return [
            'sale' => $this->sale,
            'buyerName' => $this->sale->buyer_name ?: 'Customer',
            'buyerEmail' => $this->sale->buyer_email,
            'buyerPhone' => $this->sale->buyer_phone,
            'buyerDesignation' => $this->sale->buyer_designation,
            'invoiceNumber' => $this->sale->invoice_number,
            'invoiceDate' => $this->sale->sold_at?->format('M d, Y') ?: now()->format('M d, Y'),
            'artworkTitle' => $this->sale->artwork?->title ?: 'Untitled',
            'artistName' => $this->sale->artist?->name ?: 'Unknown Artist',
            'artworkYear' => $this->sale->artwork?->year,
            'artworkStyle' => $this->sale->artwork?->style?->name,
            'artworkMedium' => $this->sale->artwork?->medium?->name,
            'paymentMethod' => $this->sale->paid_by ?: $this->sale->paymentMethod?->name,
            'lineItems' => $lineItems,
            'artworkCount' => $lineItems->count(),
            'artworkSummary' => $lineItems
                ->map(fn (array $item): string => ($item['title'] ?? 'Untitled') . ' by ' . ($item['artist_name'] ?? 'Unknown Artist') . ' - ' . $this->sale->formattedMoney($item['sold_price'] ?? 0))
                ->implode("\n"),
            'currency' => $this->sale->currency ?: 'BDT',
            'taxCountry' => $this->sale->tax_country_name,
            'taxName' => $this->sale->tax_name ?: 'Tax',
            'taxPercentage' => (float) ($this->sale->tax_percentage ?? 0),
            'taxAmount' => $taxAmount,
            'formattedTaxAmount' => $this->sale->formattedMoney($taxAmount),
            'subtotal' => $subtotal,
            'formattedSubtotal' => $this->sale->formattedMoney($subtotal),
            'amount' => $total,
            'formattedAmount' => $this->sale->formattedMoney($total),
            'supportPhone' => $this->supportPhone,
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
