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
        Schema::create('mock_test_booking_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('mock_test_registration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mock_test_exam_date_id')->nullable()->constrained()->nullOnDelete();
            $table->string('token_number')->unique();
            $table->unsignedTinyInteger('current_mock_test')->default(1);
            $table->date('exam_date');
            $table->string('slot_label')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('speaking_time')->nullable();
            $table->string('room')->nullable();
            $table->string('venue')->nullable();
            $table->string('status')->default('valid');
            $table->json('slot_snapshot')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['mock_test_registration_id', 'mock_test_exam_date_id'], 'mock_token_registration_date_unique');
            $table->index(['exam_date', 'status'], 'idx_mock_token_exam_status');
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::dropIfExists('mock_test_booking_tokens');
    }
};
