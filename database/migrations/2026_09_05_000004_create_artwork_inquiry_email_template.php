<?php

use App\Models\EmailTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        EmailTemplate::query()->updateOrCreate(
            ['key' => 'artwork_inquiry_response'],
            [
                'name' => 'Artwork inquiry response',
                'module' => 'ART Story',
                'subject' => 'Thank you for your interest in {{ $artworkTitle }}',
                'body_html' => '<p>Dear {{ $customerName }},</p><p>Thank you for contacting {{ $siteTitle }} about <strong>{{ $artworkTitle }}</strong> by {{ $artistName }}.</p><p>We have received your inquiry and our team will be in touch shortly.</p><p><strong>Your message:</strong><br>{{ $inquiryMessage }}</p><p>For further assistance, please reply to this email or contact {{ $supportEmail }}.</p><p>Regards,<br>{{ $siteTitle }}</p>',
                'body_text' => 'Dear {{ $customerName }},' . "\n\n" . 'Thank you for contacting {{ $siteTitle }} about {{ $artworkTitle }} by {{ $artistName }}.' . "\n\n" . 'We have received your inquiry and our team will be in touch shortly.' . "\n\n" . 'Your message:' . "\n" . '{{ $inquiryMessage }}' . "\n\n" . 'Regards,' . "\n" . '{{ $siteTitle }}',
                'available_variables' => [
                    'customerName' => 'Visitor name', 'customerEmail' => 'Visitor email address',
                    'customerWhatsapp' => 'Visitor WhatsApp number', 'inquiryMessage' => 'Visitor message',
                    'artworkTitle' => 'Artwork title', 'artworkCode' => 'Artwork code', 'artistName' => 'Artist name',
                    'artworkUrl' => 'Artwork page URL', 'supportEmail' => 'Site contact email',
                    'supportPhone' => 'Site contact phone', 'siteTitle' => 'Site title', 'siteLogoUrl' => 'Site logo URL',
                    'inquiry' => 'Full inquiry record', 'artwork' => 'Full artwork record',
                ],
                'is_active' => true,
                'sort_order' => 30,
            ],
        );
    }

    public function down(): void
    {
        EmailTemplate::query()->where('key', 'artwork_inquiry_response')->delete();
    }
};
