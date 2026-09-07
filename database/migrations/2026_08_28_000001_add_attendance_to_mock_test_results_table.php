<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_results', function (Blueprint $table): void {
            $table->string('attendance_status')->default('pending')->after('overall_score')->index();
            $table->string('attendance_source')->nullable()->after('attendance_status');
            $table->timestamp('attendance_marked_at')->nullable()->after('attendance_source');
            $table->foreignId('attendance_marked_by')->nullable()->after('attendance_marked_at')->constrained('users')->nullOnDelete();
            $table->foreignId('attendance_token_id')->nullable()->after('attendance_marked_by')->constrained('mock_test_booking_tokens')->nullOnDelete();
            $table->text('attendance_note')->nullable()->after('attendance_token_id');

            $table->index(['mock_test_exam_date_id', 'attendance_status'], 'idx_mock_results_exam_attendance');
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_results', function (Blueprint $table): void {
            $table->dropIndex('idx_mock_results_exam_attendance');
            $table->dropConstrainedForeignId('attendance_token_id');
            $table->dropConstrainedForeignId('attendance_marked_by');
            $table->dropColumn([
                'attendance_status',
                'attendance_source',
                'attendance_marked_at',
                'attendance_note',
            ]);
        });
    }
};
