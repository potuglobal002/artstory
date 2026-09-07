<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('email_templates')) {
            return;
        }

        DB::table('email_templates')->updateOrInsert(
            ['key' => 'artwork_sale_invoice'],
            [
                'name' => 'Artwork Sale Invoice',
                'module' => 'ART Story',
                'subject' => 'ART Story invoice {{ $invoiceNumber }}',
                'body_html' => <<<'HTML'
<h1 style="margin:0 0 18px;color:#111111;font-family:Georgia,'Times New Roman',serif;font-size:30px;line-height:1.2;">Thank you for your purchase</h1>
<p style="margin:0 0 18px;">Dear {{ $buyerName }},</p>
<p style="margin:0 0 24px;">Your ART Story invoice is attached with this email. Please keep it for your purchase record.</p>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e5e7eb;">
    <tr><td style="padding:13px 16px;background:#f9fafb;color:#6b7280;font-size:13px;">Invoice</td><td align="right" style="padding:13px 16px;background:#f9fafb;color:#111827;font-size:13px;font-weight:700;">{{ $invoiceNumber }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Invoice date</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $invoiceDate }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Artwork</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $artworkTitle }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Artist</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $artistName }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Payment</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $paymentMethod }}</td></tr>
    <tr><td style="padding:15px 16px;border-top:1px solid #111111;color:#111111;font-size:14px;font-weight:700;">Total paid</td><td align="right" style="padding:15px 16px;border-top:1px solid #111111;color:#111111;font-size:16px;font-weight:800;">{{ $formattedAmount }}</td></tr>
</table>

<p style="margin:24px 0 0;">For any assistance, reply to this email or contact {{ $supportEmail }}@if($supportPhone) / {{ $supportPhone }}@endif.</p>
<p style="margin:24px 0 0;color:#111111;">Regards,<br><strong>{{ $siteTitle }}</strong></p>
HTML,
                'body_text' => <<<'TEXT'
Dear {{ $buyerName }},

Thank you for your purchase from ART Story. Your invoice is attached with this email.

Invoice: {{ $invoiceNumber }}
Invoice date: {{ $invoiceDate }}
Artwork: {{ $artworkTitle }}
Artist: {{ $artistName }}
Payment: {{ $paymentMethod }}
Total paid: {{ $formattedAmount }}

For assistance, contact {{ $supportEmail }}@if($supportPhone) / {{ $supportPhone }}@endif.

Regards,
{{ $siteTitle }}
TEXT,
                'available_variables' => json_encode([
                    'buyerName' => 'Buyer or customer name',
                    'buyerEmail' => 'Buyer email address',
                    'buyerPhone' => 'Buyer phone number',
                    'buyerDesignation' => 'Buyer designation',
                    'invoiceNumber' => 'Invoice number',
                    'invoiceDate' => 'Invoice date',
                    'artworkTitle' => 'Sold artwork title',
                    'artistName' => 'Artwork artist name',
                    'artworkYear' => 'Artwork year',
                    'artworkStyle' => 'Artwork style',
                    'artworkMedium' => 'Artwork medium',
                    'paymentMethod' => 'Payment method',
                    'currency' => 'Currency code',
                    'amount' => 'Amount without currency label',
                    'formattedAmount' => 'Amount with BDT label',
                    'supportPhone' => 'Support phone from Site Settings',
                    'supportEmail' => 'Support email from Site Settings',
                    'siteTitle' => 'Site title from Site Settings',
                    'sale' => 'Full artwork sale record for advanced templates',
                ]),
                'is_active' => true,
                'sort_order' => 30,
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('email_templates')) {
            return;
        }

        DB::table('email_templates')->where('key', 'artwork_sale_invoice')->delete();
    }
};
