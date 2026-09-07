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
        Schema::create('mock_test_registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('test_type_id')->constrained('exam_formats')->cascadeOnDelete();
            $table->foreignId('test_format_id')->constrained('exam_formats')->cascadeOnDelete();
            $table->foreignId('writing_format_id')->constrained('exam_formats')->cascadeOnDelete();
            $table->json('selected_exam_date_ids');
            $table->json('assigned_slots')->nullable();
            $table->string('candidate_name');
            $table->string('candidate_email')->nullable();
            $table->string('candidate_mobile');
            $table->string('candidate_type')->default('outsider');
            $table->string('student_id')->nullable();
            $table->string('batch_no')->nullable();
            $table->foreignId('mock_test_pricing_id')->nullable()->constrained('mock_test_pricings')->nullOnDelete();
            $table->unsignedTinyInteger('mock_test_count');
            $table->foreignId('coupon_code_id')->nullable()->constrained('coupon_codes')->nullOnDelete();
            $table->decimal('original_amount', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('amount', 12, 2);
            $table->string('coupon_code')->nullable();
            $table->json('coupon_snapshot')->nullable();
            $table->string('currency', 3)->default('BDT');
            $table->string('gateway_code')->nullable();
            $table->string('gateway_name')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('registration_status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('gateway_payment_id')->nullable();
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['payment_status', 'registration_status'], 'idx_mock_reg_statuses');
            $table->index(['candidate_mobile', 'candidate_email'], 'idx_mock_reg_candidate');
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::dropIfExists('mock_test_registrations');
    }
};
