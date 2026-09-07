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
        Schema::create('mock_test_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mock_test_registration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mock_test_exam_date_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('test_type_id')->nullable()->constrained('exam_formats')->nullOnDelete();
            $table->foreignId('test_format_id')->nullable()->constrained('exam_formats')->nullOnDelete();
            $table->foreignId('writing_format_id')->nullable()->constrained('exam_formats')->nullOnDelete();
            $table->string('candidate_name');
            $table->string('candidate_email')->nullable();
            $table->string('candidate_mobile', 40);
            $table->json('slot_snapshot')->nullable();
            $table->unsignedTinyInteger('listening_correct_answers')->nullable();
            $table->decimal('listening_score', 3, 1)->nullable();
            $table->unsignedTinyInteger('reading_correct_answers')->nullable();
            $table->decimal('reading_score', 3, 1)->nullable();
            $table->decimal('speaking_score', 3, 1)->nullable();
            $table->decimal('writing_task_1_score', 3, 1)->nullable();
            $table->decimal('writing_task_2_score', 3, 1)->nullable();
            $table->decimal('writing_score', 3, 1)->nullable();
            $table->decimal('overall_score', 3, 1)->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['mock_test_registration_id', 'mock_test_exam_date_id'], 'mock_result_registration_exam_unique');
            $table->index(['mock_test_exam_date_id', 'status']);
            $table->index(['candidate_mobile', 'candidate_email']);
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::dropIfExists('mock_test_results');
    }
};
