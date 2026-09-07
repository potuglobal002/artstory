<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_booking_tokens', function (Blueprint $table): void {
            try {
                $table->dropUnique('mock_test_booking_tokens_token_number_unique');
            } catch (Throwable) {
                // Some local databases may already have this index removed.
            }

            try {
                $table->index('token_number', 'idx_mock_token_number');
            } catch (Throwable) {
                // Keep migration idempotent for local development databases.
            }
        });

        DB::table('mock_test_booking_tokens')
            ->join('mock_test_registrations', 'mock_test_registrations.id', '=', 'mock_test_booking_tokens.mock_test_registration_id')
            ->select([
                'mock_test_booking_tokens.id',
                'mock_test_registrations.candidate_mobile',
            ])
            ->orderBy('mock_test_booking_tokens.id')
            ->get()
            ->each(function (object $row): void {
                $digits = preg_replace('/\D+/', '', (string) $row->candidate_mobile) ?: '';
                $tokenNumber = ltrim($digits, '0') ?: $digits;

                DB::table('mock_test_booking_tokens')
                    ->where('id', $row->id)
                    ->update(['token_number' => $tokenNumber]);
            });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_booking_tokens', function (Blueprint $table): void {
            try {
                $table->dropIndex('idx_mock_token_number');
            } catch (Throwable) {
            }
        });
    }
};
