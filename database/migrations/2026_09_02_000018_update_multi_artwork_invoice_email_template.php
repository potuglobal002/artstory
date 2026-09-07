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

        DB::table('email_templates')
            ->where('key', 'artwork_sale_invoice')
            ->update([
                'subject' => '{{ $siteTitle }} invoice {{ $invoiceNumber }}',
                'body_html' => <<<'HTML'
<h1 style="margin:0 0 18px;color:#111111;font-family:Georgia,'Times New Roman',serif;font-size:30px;line-height:1.2;">Thank you for your purchase</h1>
<p style="margin:0 0 18px;">Dear {{ $buyerName }},</p>
<p style="margin:0 0 24px;">Your {{ $siteTitle }} invoice is attached with this email. Please keep it for your purchase record.</p>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e5e7eb;">
    <tr><td style="padding:13px 16px;background:#f9fafb;color:#6b7280;font-size:13px;">Invoice</td><td align="right" style="padding:13px 16px;background:#f9fafb;color:#111827;font-size:13px;font-weight:700;">{{ $invoiceNumber }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Invoice date</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $invoiceDate }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Artworks</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $artworkCount }} item(s)</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Payment</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $paymentMethod }}</td></tr>
    @foreach($lineItems as $item)
        <tr>
            <td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">
                {{ $item['title'] ?? 'Untitled' }}
                <div style="margin-top:4px;color:#6b7280;font-size:12px;font-weight:400;">{{ $item['artist_name'] ?? 'Unknown Artist' }}</div>
            </td>
            <td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">BDT {{ number_format((float) ($item['sold_price'] ?? 0), 0) }}</td>
        </tr>
    @endforeach
    <tr><td style="padding:15px 16px;border-top:1px solid #111111;color:#111111;font-size:14px;font-weight:700;">Total paid</td><td align="right" style="padding:15px 16px;border-top:1px solid #111111;color:#111111;font-size:16px;font-weight:800;">{{ $formattedAmount }}</td></tr>
</table>

<p style="margin:24px 0 0;">For any assistance, reply to this email or contact {{ $supportEmail }}@if($supportPhone) / {{ $supportPhone }}@endif.</p>
<p style="margin:24px 0 0;color:#111111;">Regards,<br><strong>{{ $siteTitle }}</strong></p>
HTML,
                'body_text' => <<<'TEXT'
Dear {{ $buyerName }},

Thank you for your purchase from {{ $siteTitle }}. Your invoice is attached with this email.

Invoice: {{ $invoiceNumber }}
Invoice date: {{ $invoiceDate }}
Payment: {{ $paymentMethod }}
Artworks:
{{ $artworkSummary }}

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
                    'lineItems' => 'Purchased artwork rows for advanced templates',
                    'artworkCount' => 'Number of purchased artworks',
                    'artworkSummary' => 'Plain text purchased artwork summary',
                    'artworkTitle' => 'Primary sold artwork title',
                    'artistName' => 'Primary artwork artist name',
                    'artworkYear' => 'Primary artwork year',
                    'artworkStyle' => 'Primary artwork style',
                    'artworkMedium' => 'Primary artwork medium',
                    'paymentMethod' => 'Payment method',
                    'currency' => 'Currency code',
                    'amount' => 'Total amount without currency label',
                    'formattedAmount' => 'Total amount with BDT label',
                    'supportPhone' => 'Support phone from Site Settings',
                    'supportEmail' => 'Support email from Site Settings',
                    'siteTitle' => 'Site title from Site Settings',
                    'siteLogoUrl' => 'Logo URL from Site Settings',
                    'sale' => 'Full artwork sale record for advanced templates',
                ]),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
