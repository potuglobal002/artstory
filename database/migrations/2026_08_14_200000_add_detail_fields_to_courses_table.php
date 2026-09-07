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
        Schema::table('courses', function (Blueprint $table) {
            $table->text('who_for')->nullable()->after('description');
            $table->text('lead')->nullable()->after('who_for');
            $table->string('fee_note')->nullable()->after('fee');
            $table->string('batch_size')->nullable()->after('target_score');
            $table->string('campus')->nullable()->after('batch_size');
            $table->json('includes')->nullable()->after('outcomes');
            $table->json('modules')->nullable()->after('includes');
            $table->json('schedule')->nullable()->after('modules');
            $table->json('faq')->nullable()->after('schedule');
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'who_for',
                'lead',
                'fee_note',
                'batch_size',
                'campus',
                'includes',
                'modules',
                'schedule',
                'faq',
            ]);
        });
    }
};
