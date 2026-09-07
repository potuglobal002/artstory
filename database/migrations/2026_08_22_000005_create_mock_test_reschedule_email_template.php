<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        DB::table('email_templates')->updateOrInsert(
            ['key' => 'mock_test_registration_rescheduled'],
            [
                'name' => 'Mock Test Reschedule Confirmation',
                'module' => 'Mock Test',
                'subject' => 'STS mock test schedule updated for {{ $candidateName }}',
                'body_html' => <<<'HTML'
<h1 style="margin:0 0 16px;color:#142033;font-size:28px;line-height:1.25;">Your mock test schedule has been updated</h1>
<p>Dear {{ $candidateName }},</p>
<p>Your STS Institute IELTS mock test date has been rescheduled by the admin team.</p>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:22px 0;border:1px solid #e3eaf3;border-radius:12px;overflow:hidden;">
    <tr><td style="padding:12px 14px;background:#f8fafc;color:#5d6b82;">Previous date</td><td style="padding:12px 14px;background:#f8fafc;text-align:right;font-weight:700;">{{ $previousDate }}</td></tr>
    <tr><td style="padding:12px 14px;border-top:1px solid #e3eaf3;color:#5d6b82;">New date</td><td style="padding:12px 14px;border-top:1px solid #e3eaf3;text-align:right;font-weight:700;">{{ $newDate }}</td></tr>
    <tr><td style="padding:12px 14px;border-top:1px solid #e3eaf3;color:#5d6b82;">New time</td><td style="padding:12px 14px;border-top:1px solid #e3eaf3;text-align:right;font-weight:700;">{{ $newStartTime }}@if($newEndTime) - {{ $newEndTime }}@endif</td></tr>
    <tr><td style="padding:12px 14px;border-top:1px solid #e3eaf3;color:#5d6b82;">Test type</td><td style="padding:12px 14px;border-top:1px solid #e3eaf3;text-align:right;font-weight:700;">{{ $testType }}</td></tr>
</table>

@if($rescheduleReason)
<div style="margin-top:20px;padding:16px 18px;background:#fff8f2;border:1px solid #ffd9bd;border-radius:12px;">
    <strong style="display:block;margin-bottom:8px;color:#142033;">Reason</strong>
    <p style="margin:0;color:#4c5b72;line-height:1.6;">{{ $rescheduleReason }}</p>
</div>
@endif

<p style="margin:24px 0 0;color:#4c5b72;line-height:1.7;">Please arrive 20 minutes early and bring a valid photo ID. Need help? Call {{ $supportPhone }} or reply to {{ $supportEmail }}.</p>
HTML,
                'body_text' => <<<'TEXT'
Dear {{ $candidateName }},

Your STS Institute IELTS mock test date has been rescheduled.

Previous date: {{ $previousDate }}
New date: {{ $newDate }}
New time: {{ $newStartTime }}@if($newEndTime) - {{ $newEndTime }}@endif
Test type: {{ $testType }}
@if($rescheduleReason)
Reason: {{ $rescheduleReason }}
@endif

Please arrive 20 minutes early and bring a valid photo ID.
Need help? Call {{ $supportPhone }} or reply to {{ $supportEmail }}.
TEXT,
                'available_variables' => json_encode([
                    'candidateName' => 'Candidate full name',
                    'candidateEmail' => 'Candidate email address',
                    'candidateMobile' => 'Candidate mobile number',
                    'testType' => 'Selected IELTS test type',
                    'testFormat' => 'Selected test format',
                    'writingFormat' => 'Selected writing test format',
                    'previousDate' => 'Old mock test date',
                    'newDate' => 'New mock test date',
                    'newSlot' => 'New slot label',
                    'newStartTime' => 'New slot start time',
                    'newEndTime' => 'New slot end time',
                    'rescheduleReason' => 'Reason entered by admin',
                    'supportPhone' => 'STS support phone from Site Settings',
                    'supportEmail' => 'STS support email from Site Settings',
                ]),
                'is_active' => true,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        DB::table('email_templates')->where('key', 'mock_test_registration_rescheduled')->delete();
    }
};
