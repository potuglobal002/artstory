<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        DB::table('email_templates')
            ->where('key', 'mock_test_registration_confirmed')
            ->update([
                'available_variables' => json_encode([
                    'candidateName' => 'Candidate full name',
                    'candidateEmail' => 'Candidate email address',
                    'candidateMobile' => 'Candidate mobile number',
                    'testType' => 'Selected IELTS test type',
                    'testFormat' => 'Selected test format',
                    'writingFormat' => 'Selected writing test format',
                    'mockTestCount' => 'Number of mock tests',
                    'currency' => 'Payment currency, for example BDT',
                    'amount' => 'Final paid amount after discount',
                    'paymentMethod' => 'Cash or payment gateway name',
                    'supportPhone' => 'STS support phone from Site Settings',
                    'supportEmail' => 'STS support email from Site Settings',
                    'slotRows' => 'Selected dates and assigned times. Use with @foreach.',
                    'registration' => 'Full registration record for advanced templates',
                ]),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        //
    }
};
