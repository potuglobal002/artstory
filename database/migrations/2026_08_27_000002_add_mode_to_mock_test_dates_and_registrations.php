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
        Schema::table('mock_test_exam_dates', function (Blueprint $table): void {
            if (! Schema::hasColumn('mock_test_exam_dates', 'exam_mode')) {
                $table->string('exam_mode', 20)->default('online')->after('writing_format_ids');
            }
        });

        Schema::table('mock_test_registrations', function (Blueprint $table): void {
            if (! Schema::hasColumn('mock_test_registrations', 'booking_mode')) {
                $table->string('booking_mode', 20)->default('online')->after('writing_format_id');
            }
        });

        DB::table('mock_test_exam_dates')
            ->whereNull('exam_mode')
            ->orWhere('exam_mode', '')
            ->update(['exam_mode' => 'online']);

        DB::table('mock_test_registrations')
            ->whereNull('booking_mode')
            ->orWhere('booking_mode', '')
            ->update(['booking_mode' => 'online']);

        Schema::table('mock_test_exam_dates', function (Blueprint $table): void {
            try {
                $table->dropUnique('mock_test_exam_dates_exam_date_unique');
            } catch (Throwable) {
                // The unique index may have been removed already in some local databases.
            }

            $table->unique(['exam_mode', 'exam_date'], 'mock_test_exam_dates_mode_date_unique');
            $table->index(['exam_mode', 'is_active', 'exam_date'], 'idx_mock_test_dates_mode_active_date');
        });

        Schema::table('mock_test_registrations', function (Blueprint $table): void {
            $table->index(['booking_mode', 'payment_status', 'registration_status'], 'idx_mock_reg_mode_statuses');
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_registrations', function (Blueprint $table): void {
            try {
                $table->dropIndex('idx_mock_reg_mode_statuses');
            } catch (Throwable) {
            }

            if (Schema::hasColumn('mock_test_registrations', 'booking_mode')) {
                $table->dropColumn('booking_mode');
            }
        });

        Schema::table('mock_test_exam_dates', function (Blueprint $table): void {
            try {
                $table->dropIndex('idx_mock_test_dates_mode_active_date');
            } catch (Throwable) {
            }

            try {
                $table->dropUnique('mock_test_exam_dates_mode_date_unique');
            } catch (Throwable) {
            }

            if (Schema::hasColumn('mock_test_exam_dates', 'exam_mode')) {
                $table->dropColumn('exam_mode');
            }

            try {
                $table->unique('exam_date', 'mock_test_exam_dates_exam_date_unique');
            } catch (Throwable) {
            }
        });
    }
};
