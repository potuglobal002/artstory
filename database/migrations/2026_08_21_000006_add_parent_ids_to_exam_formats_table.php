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
        if (! Schema::hasColumn('exam_formats', 'parent_ids')) {
            Schema::table('exam_formats', function (Blueprint $table) {
                $table->json('parent_ids')->nullable()->after('section_sort_order');
            });
        }

        $academicId = DB::table('exam_formats')->where('slug', 'ielts-academic')->value('id');
        $generalId = DB::table('exam_formats')->where('slug', 'ielts-general-training')->value('id');
        $computerId = DB::table('exam_formats')->where('slug', 'ielts-on-computer')->value('id');
        $writingPaperId = DB::table('exam_formats')->where('slug', 'writing-on-paper')->value('id');
        $writingComputerId = DB::table('exam_formats')->where('slug', 'writing-on-computer')->value('id');

        if ($computerId && $academicId && $generalId) {
            DB::table('exam_formats')
                ->where('id', $computerId)
                ->update([
                    'parent_ids' => json_encode([(int) $academicId, (int) $generalId]),
                    'updated_at' => now(),
                ]);
        }

        if ($computerId) {
            DB::table('exam_formats')
                ->whereIn('id', array_filter([$writingPaperId, $writingComputerId]))
                ->update([
                    'parent_ids' => json_encode([(int) $computerId]),
                    'updated_at' => now(),
                ]);

            DB::table('mock_test_exam_dates')
                ->update([
                    'test_format_id' => $computerId,
                    'test_format_ids' => json_encode([(int) $computerId]),
                    'writing_format_ids' => json_encode(array_values(array_filter([(int) $writingPaperId, (int) $writingComputerId]))),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        if (Schema::hasColumn('exam_formats', 'parent_ids')) {
            Schema::table('exam_formats', function (Blueprint $table) {
                $table->dropColumn('parent_ids');
            });
        }
    }
};
