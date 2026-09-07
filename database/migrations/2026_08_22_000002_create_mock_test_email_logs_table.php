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
        Schema::create('mock_test_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_registration_id')->constrained()->cascadeOnDelete();
            $table->string('candidate_email')->nullable();
            $table->string('subject');
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index(['mock_test_registration_id', 'status'], 'idx_mock_email_registration_status');
            $table->index(['candidate_email', 'status'], 'idx_mock_email_candidate_status');
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::dropIfExists('mock_test_email_logs');
    }
};
