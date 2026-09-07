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
            ['key' => 'artist_artwork_published'],
            [
                'name' => 'Artist Artwork Published',
                'module' => 'ART Story',
                'subject' => '{{ $siteTitle }} artwork approved: {{ $artworkTitle }}',
                'body_html' => <<<'HTML'
<h1 style="margin:0 0 18px;color:#111111;font-family:Georgia,'Times New Roman',serif;font-size:30px;line-height:1.2;">Your artwork is now published</h1>
<p style="margin:0 0 18px;">Dear {{ $artistName }},</p>
<p style="margin:0 0 24px;">Thank you for submitting your artwork to {{ $siteTitle }}. Our team has reviewed and approved it, and it is now visible in the public artwork collection.</p>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e5e7eb;margin-bottom:24px;">
    <tr><td style="padding:13px 16px;background:#f9fafb;color:#6b7280;font-size:13px;">Artwork</td><td align="right" style="padding:13px 16px;background:#f9fafb;color:#111827;font-size:13px;font-weight:700;">{{ $artworkTitle }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Year</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $artworkYear ?: '-' }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Style</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $artworkStyle ?: '-' }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Subject style</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $artworkSubjectStyle ?: '-' }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Medium</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $artworkMedium ?: '-' }}</td></tr>
    <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Size</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $artworkSize ?: '-' }}</td></tr>
</table>

<a href="{{ $dashboardUrl }}" style="display:inline-block;background:#111111;color:#ffffff;font-size:13px;font-weight:800;letter-spacing:1px;padding:14px 20px;text-decoration:none;text-transform:uppercase;">Open artist dashboard</a>

<p style="margin:24px 0 0;">For any assistance, reply to this email or contact {{ $supportEmail }}.</p>
<p style="margin:24px 0 0;color:#111111;">Regards,<br><strong>{{ $siteTitle }}</strong></p>
HTML,
                'body_text' => <<<'TEXT'
Dear {{ $artistName }},

Your artwork has been approved and is now published on {{ $siteTitle }}.

Artwork: {{ $artworkTitle }}
Year: {{ $artworkYear ?: '-' }}
Style: {{ $artworkStyle ?: '-' }}
Subject style: {{ $artworkSubjectStyle ?: '-' }}
Medium: {{ $artworkMedium ?: '-' }}
Size: {{ $artworkSize ?: '-' }}

Dashboard: {{ $dashboardUrl }}

For assistance, contact {{ $supportEmail }}.

Regards,
{{ $siteTitle }}
TEXT,
                'available_variables' => json_encode([
                    'artistName' => 'Artist name',
                    'artworkTitle' => 'Approved artwork title',
                    'artworkYear' => 'Approved artwork year',
                    'artworkStyle' => 'Approved artwork style',
                    'artworkSubjectStyle' => 'Approved artwork subject style',
                    'artworkMedium' => 'Approved artwork medium',
                    'artworkSize' => 'Approved artwork size',
                    'dashboardUrl' => 'Frontend artist dashboard URL',
                    'supportEmail' => 'Support email from Site Settings',
                    'siteTitle' => 'Site title from Site Settings',
                    'siteLogoUrl' => 'Logo URL from Site Settings',
                    'artwork' => 'Full artwork record for advanced templates',
                    'artist' => 'Full artist record for advanced templates',
                ]),
                'is_active' => true,
                'sort_order' => 32,
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

        DB::table('email_templates')->where('key', 'artist_artwork_published')->delete();
    }
};
