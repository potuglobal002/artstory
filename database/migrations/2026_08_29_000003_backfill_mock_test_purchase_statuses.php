<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        if (! Schema::hasColumn('mock_test_registrations', 'mock_test_purchase_status')) {
            return;
        }

        DB::table('mock_test_registrations')
            ->whereNull('mock_test_purchase_status')
            ->orderBy('id')
            ->get(['id', 'mock_test_count', 'amount', 'coupon_code'])
            ->each(function (object $registration): void {
                $count = max(1, min(5, (int) $registration->mock_test_count));
                $amount = (float) $registration->amount;
                $coupon = strtoupper((string) $registration->coupon_code);

                $status = match (true) {
                    str_contains($coupon, 'FMOCK1') || ($amount <= 0 && $count === 1) => 'free_with_reg_1',
                    str_contains($coupon, 'FMOCK2') || ($amount <= 0 && $count === 2) => 'free_with_reg_2',
                    $amount > 0 && $count === 2 => 'purchase_2_mocks',
                    $amount > 0 && $count === 3 => 'purchase_3_mocks',
                    $amount > 0 && $count === 4 => 'purchase_4_mocks',
                    $amount > 0 && $count === 5 => 'purchase_5_mocks',
                    default => 'purchase_1_mock',
                };

                DB::table('mock_test_registrations')
                    ->where('id', $registration->id)
                    ->update([
                        'mock_test_purchase_status' => $status,
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        //
    }
};
