<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        $academicId = DB::table('exam_formats')->where('slug', 'ielts-academic')->value('id');
        $generalId = DB::table('exam_formats')->where('slug', 'ielts-general-training')->value('id');
        $computerId = DB::table('exam_formats')->where('slug', 'ielts-on-computer')->value('id');
        $writingPaperId = DB::table('exam_formats')->where('slug', 'writing-on-paper')->value('id');
        $writingComputerId = DB::table('exam_formats')->where('slug', 'writing-on-computer')->value('id');

        DB::table('exam_formats')->where('slug', 'ielts-on-paper')->update([
            'is_active' => false,
            'updated_at' => now(),
        ]);

        DB::table('exam_formats')->where('slug', 'ielts-on-computer')->update([
            'section_key' => 'test-format',
            'section_title' => 'Select your test format',
            'section_sort_order' => 20,
            'parent_ids' => json_encode(array_values(array_filter([(int) $academicId, (int) $generalId]))),
            'is_active' => true,
            'updated_at' => now(),
        ]);

        DB::table('exam_formats')->whereIn('slug', ['writing-on-paper', 'writing-on-computer'])->update([
            'section_key' => 'writing-format',
            'section_title' => 'Select your IELTS Writing test format',
            'section_sort_order' => 30,
            'parent_ids' => json_encode(array_values(array_filter([(int) $computerId]))),
            'is_active' => true,
            'updated_at' => now(),
        ]);

        DB::table('mock_test_exam_dates')->update([
            'test_type_ids' => json_encode(array_values(array_filter([(int) $academicId, (int) $generalId]))),
            'test_format_ids' => json_encode(array_values(array_filter([(int) $computerId]))),
            'writing_format_ids' => json_encode(array_values(array_filter([(int) $writingPaperId, (int) $writingComputerId]))),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        DB::table('exam_formats')->where('slug', 'ielts-on-paper')->update([
            'is_active' => true,
            'updated_at' => now(),
        ]);
    }
};
