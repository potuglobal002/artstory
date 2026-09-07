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
        Schema::create('mock_test_exam_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_format_id')->nullable()->constrained('exam_formats')->nullOnDelete();
            $table->string('title')->nullable();
            $table->date('exam_date');
            $table->date('registration_deadline')->nullable();
            $table->string('venue')->nullable();
            $table->decimal('fee_amount', 12, 2)->nullable();
            $table->string('currency', 3)->default('BDT');
            $table->json('time_slots')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'exam_date', 'sort_order'], 'idx_mock_test_dates_active_date');
            $table->index(['exam_format_id', 'is_active', 'exam_date'], 'idx_mock_test_dates_format_active_date');
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::dropIfExists('mock_test_exam_dates');
    }
};
