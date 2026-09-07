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
            $table->decimal('fee_amount', 12, 2)->nullable()->after('fee');
        });

        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sandbox')->default(true);
            $table->boolean('use_sandbox_simulator')->default(true);
            $table->string('currency', 3)->default('BDT');
            $table->string('merchant_id')->nullable();
            $table->string('store_id')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->string('base_url')->nullable();
            $table->string('checkout_url')->nullable();
            $table->json('extra_config')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('student_name');
            $table->string('student_email')->nullable();
            $table->string('student_phone');
            $table->string('guardian_phone')->nullable();
            $table->string('address')->nullable();
            $table->text('note')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('gateway_code')->nullable();
            $table->string('gateway_name')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('enrollment_status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('gateway_payment_id')->nullable();
            $table->json('gateway_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('payment_gateway_settings');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('fee_amount');
        });
    }
};
