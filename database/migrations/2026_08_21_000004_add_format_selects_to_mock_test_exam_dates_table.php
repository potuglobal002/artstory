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
        if (! Schema::hasColumn('mock_test_exam_dates', 'test_type_id')) {
            Schema::table('mock_test_exam_dates', function (Blueprint $table) {
                $table->foreignId('test_type_id')->nullable()->after('exam_format_id')->constrained('exam_formats')->nullOnDelete();
                $table->foreignId('test_format_id')->nullable()->after('test_type_id')->constrained('exam_formats')->nullOnDelete();
                $table->foreignId('writing_format_id')->nullable()->after('test_format_id')->constrained('exam_formats')->nullOnDelete();

                $table->index(['test_type_id', 'test_format_id', 'writing_format_id', 'is_active', 'exam_date'], 'idx_mock_test_dates_choices_active_date');
            });
        }

        $academicId = DB::table('exam_formats')->where('slug', 'ielts-academic')->value('id');
        $paperId = DB::table('exam_formats')->where('slug', 'ielts-on-paper')->value('id');
        $writingPaperId = DB::table('exam_formats')->where('slug', 'writing-on-paper')->value('id');

        DB::table('mock_test_exam_dates')
            ->whereNull('test_type_id')
            ->update([
                'test_type_id' => DB::raw('COALESCE(exam_format_id, ' . ((int) $academicId) . ')'),
                'test_format_id' => $paperId,
                'writing_format_id' => $writingPaperId,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        if (Schema::hasColumn('mock_test_exam_dates', 'test_type_id')) {
            Schema::table('mock_test_exam_dates', function (Blueprint $table) {
                $table->dropForeign(['test_type_id']);
                $table->dropForeign(['test_format_id']);
                $table->dropForeign(['writing_format_id']);
                $table->dropIndex('idx_mock_test_dates_choices_active_date');
                $table->dropColumn(['test_type_id', 'test_format_id', 'writing_format_id']);
            });
        }
    }
};
