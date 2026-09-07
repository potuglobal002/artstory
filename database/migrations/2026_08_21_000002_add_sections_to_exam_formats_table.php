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
        if (! Schema::hasColumn('exam_formats', 'section_key')) {
            Schema::table('exam_formats', function (Blueprint $table) {
                $table->string('section_key')->default('test-type')->after('id');
                $table->string('section_title')->default('Which IELTS test do you need?')->after('section_key');
                $table->text('section_description')->nullable()->after('section_title');
                $table->unsignedInteger('section_sort_order')->default(10)->after('section_description');
                $table->string('badge_label')->nullable()->after('description');
                $table->string('badge_icon')->nullable()->after('badge_label');
                $table->string('badge_color')->default('green')->after('badge_icon');
            });
        }

        if (! $this->indexExists('idx_exam_formats_section_active_sort')) {
            Schema::table('exam_formats', function (Blueprint $table) {
                $table->index(['section_key', 'is_active', 'section_sort_order', 'sort_order'], 'idx_exam_formats_section_active_sort');
            });
        }

        DB::table('exam_formats')
            ->whereIn('slug', ['ielts-academic', 'ielts-general-training'])
            ->update([
                'section_key' => 'test-type',
                'section_title' => 'Which IELTS test do you need?',
                'section_sort_order' => 10,
                'updated_at' => now(),
            ]);

        foreach ([
            [
                'section_key' => 'test-format',
                'section_title' => 'Select your test format',
                'section_sort_order' => 20,
                'title' => 'IELTS on Paper',
                'slug' => 'ielts-on-paper',
                'icon' => 'document-text',
                'description' => 'Complete the Listening, Reading and Writing tasks on paper at a test centre. The Speaking test is face to face with an examiner.',
                'badge_label' => null,
                'badge_icon' => null,
                'badge_color' => 'green',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'section_key' => 'test-format',
                'section_title' => 'Select your test format',
                'section_sort_order' => 20,
                'title' => 'IELTS on Computer',
                'slug' => 'ielts-on-computer',
                'icon' => 'computer-desktop',
                'description' => 'Complete Listening and Reading on a computer at a test centre. Choose computer or paper for Writing. Speaking is conducted with an examiner either face-to-face or via video call.',
                'badge_label' => 'Fastest results',
                'badge_icon' => 'bolt',
                'badge_color' => 'green',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'section_key' => 'writing-format',
                'section_title' => 'Select your IELTS Writing test format',
                'section_sort_order' => 30,
                'title' => 'Writing on Paper',
                'slug' => 'writing-on-paper',
                'icon' => 'pencil',
                'description' => 'Complete the Writing test using pen and paper at a test centre. Reading and Listening are taken on a computer. If your location is not listed, choose Writing on Computer instead.',
                'badge_label' => null,
                'badge_icon' => null,
                'badge_color' => 'green',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'section_key' => 'writing-format',
                'section_title' => 'Select your IELTS Writing test format',
                'section_sort_order' => 30,
                'title' => 'Writing on Computer',
                'slug' => 'writing-on-computer',
                'icon' => 'keyboard',
                'description' => 'Complete the IELTS Writing test on a computer at a test centre.',
                'badge_label' => 'Fastest results',
                'badge_icon' => 'bolt',
                'badge_color' => 'green',
                'is_active' => true,
                'sort_order' => 20,
            ],
        ] as $row) {
            DB::table('exam_formats')->updateOrInsert(
                ['slug' => $row['slug']],
                $row + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        DB::table('exam_formats')
            ->whereIn('slug', ['ielts-on-paper', 'ielts-on-computer', 'writing-on-paper', 'writing-on-computer'])
            ->delete();

        Schema::table('exam_formats', function (Blueprint $table) {
            $table->dropIndex('idx_exam_formats_section_active_sort');
            $table->dropColumn([
                'section_key',
                'section_title',
                'section_description',
                'section_sort_order',
                'badge_label',
                'badge_icon',
                'badge_color',
            ]);
        });
    }

    private function indexExists(string $indexName): bool
    {
        return collect(DB::select('SHOW INDEX FROM `exam_formats`'))
            ->contains(fn (object $index): bool => (string) $index->Key_name === $indexName);
    }
};
