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
        if (! Schema::hasColumn('mock_test_exam_dates', 'test_type_ids')) {
            Schema::table('mock_test_exam_dates', function (Blueprint $table) {
                $table->json('test_type_ids')->nullable()->after('writing_format_id');
                $table->json('test_format_ids')->nullable()->after('test_type_ids');
                $table->json('writing_format_ids')->nullable()->after('test_format_ids');
            });
        }

        DB::table('mock_test_exam_dates')
            ->orderBy('id')
            ->each(function (object $row): void {
                DB::table('mock_test_exam_dates')
                    ->where('id', $row->id)
                    ->update([
                        'test_type_ids' => $row->test_type_id ? json_encode([(int) $row->test_type_id]) : null,
                        'test_format_ids' => $row->test_format_id ? json_encode([(int) $row->test_format_id]) : null,
                        'writing_format_ids' => $row->writing_format_id ? json_encode([(int) $row->writing_format_id]) : null,
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        if (Schema::hasColumn('mock_test_exam_dates', 'test_type_ids')) {
            Schema::table('mock_test_exam_dates', function (Blueprint $table) {
                $table->dropColumn(['test_type_ids', 'test_format_ids', 'writing_format_ids']);
            });
        }
    }
};
