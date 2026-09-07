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
            ['key' => 'artist_account_approved'],
            [
                'name' => 'Artist Account Approved',
                'module' => 'ART Story',
                'subject' => '{{ $siteTitle }} artist account approved',
                'body_html' => <<<'HTML'
<h1 style="margin:0 0 18px;color:#111111;font-family:Georgia,'Times New Roman',serif;font-size:30px;line-height:1.2;">Welcome to your artist dashboard</h1>
<p style="margin:0 0 18px;">Dear {{ $artistName }},</p>
<p style="margin:0 0 24px;">Your artist profile has been approved. You can now log in and upload artworks for review.</p>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #e5e7eb;margin-bottom:24px;">
    <tr><td style="padding:13px 16px;background:#f9fafb;color:#6b7280;font-size:13px;">Login email</td><td align="right" style="padding:13px 16px;background:#f9fafb;color:#111827;font-size:13px;font-weight:700;">{{ $artistEmail }}</td></tr>
    @if ($temporaryPassword)
        <tr><td style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:13px;">Temporary password</td><td align="right" style="padding:13px 16px;border-top:1px solid #e5e7eb;color:#111827;font-size:13px;font-weight:700;">{{ $temporaryPassword }}</td></tr>
    @endif
</table>

<a href="{{ $loginUrl }}" style="display:inline-block;background:#111111;color:#ffffff;font-size:13px;font-weight:800;letter-spacing:1px;padding:14px 20px;text-decoration:none;text-transform:uppercase;">Log in to dashboard</a>

<p style="margin:24px 0 0;">@if ($temporaryPassword) For security, please change this password after your first login. @else Use your existing account password to sign in. @endif For help, reply to {{ $supportEmail }}.</p>
<p style="margin:24px 0 0;color:#111111;">Regards,<br><strong>{{ $siteTitle }}</strong></p>
HTML,
                'body_text' => <<<'TEXT'
Dear {{ $artistName }},

Your artist profile has been approved. You can now log in and upload artworks for review.

Login email: {{ $artistEmail }}
@if ($temporaryPassword)
Temporary password: {{ $temporaryPassword }}
@else
Use your existing account password to sign in.
@endif

Login: {{ $loginUrl }}

For help, reply to {{ $supportEmail }}.

Regards,
{{ $siteTitle }}
TEXT,
                'available_variables' => json_encode([
                    'artistName' => 'Approved artist name',
                    'artistEmail' => 'Artist login email',
                    'temporaryPassword' => 'Temporary password for new artist accounts',
                    'loginUrl' => 'Frontend artist login page',
                    'supportEmail' => 'Support email from Site Settings',
                    'siteTitle' => 'Site title from Site Settings',
                    'artist' => 'Full artist record for advanced templates',
                ]),
                'is_active' => true,
                'sort_order' => 31,
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

        DB::table('email_templates')->where('key', 'artist_account_approved')->delete();
    }
};
