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
        Schema::create('exam_formats', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon')->default('academic-cap');
            $table->text('description')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->boolean('open_in_new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order'], 'idx_exam_formats_active_sort');
        });

        DB::table('exam_formats')->insert([
            [
                'title' => 'IELTS Academic',
                'slug' => 'ielts-academic',
                'icon' => 'academic-cap',
                'description' => 'For people applying to universities or professional registration. Focuses on English language skills used in an academic context.',
                'cta_label' => 'View academic mock test',
                'cta_url' => '/course/ielts-academic',
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'IELTS General Training',
                'slug' => 'ielts-general-training',
                'icon' => 'briefcase',
                'description' => 'For migration and everyday use in an English-speaking environment. Focuses on practical, real-life English skills.',
                'cta_label' => 'View general training mock test',
                'cta_url' => '/course/ielts-general-training',
                'is_active' => true,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::dropIfExists('exam_formats');
    }
};
