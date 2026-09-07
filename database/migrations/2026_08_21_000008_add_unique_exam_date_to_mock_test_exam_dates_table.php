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
        $duplicates = DB::table('mock_test_exam_dates')
            ->select('exam_date')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('exam_date')
            ->having('total', '>', 1)
            ->exists();

        if ($duplicates) {
            return;
        }

        Schema::table('mock_test_exam_dates', function (Blueprint $table) {
            $table->unique('exam_date', 'mock_test_exam_dates_exam_date_unique');
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('mock_test_exam_dates', function (Blueprint $table) {
            $table->dropUnique('mock_test_exam_dates_exam_date_unique');
        });
    }
};
