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
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('applies_to')->default('both');
            $table->string('discount_type')->default('fixed');
            $table->decimal('discount_value', 12, 2);
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_customer_limit')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['applies_to', 'is_active'], 'idx_coupon_codes_scope_active');
            $table->index(['starts_at', 'expires_at'], 'idx_coupon_codes_active_window');
        });

        Schema::create('coupon_code_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_code_id')->constrained('coupon_codes')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('applies_to');
            $table->string('discount_type');
            $table->decimal('discount_value', 12, 2);
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_customer_limit')->nullable();
            $table->unsignedInteger('version');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('changed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['coupon_code_id', 'version'], 'idx_coupon_code_versions_record_version');
            $table->index(['code', 'version'], 'idx_coupon_code_versions_code_version');
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_code_id')->nullable()->constrained('coupon_codes')->nullOnDelete();
            $table->string('coupon_code');
            $table->string('module');
            $table->nullableMorphs('redeemable');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('original_amount', 12, 2);
            $table->decimal('discount_amount', 12, 2);
            $table->decimal('final_amount', 12, 2);
            $table->json('coupon_snapshot')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamps();

            $table->index(['coupon_code_id', 'module'], 'idx_coupon_redemptions_coupon_module');
            $table->index(['customer_email', 'customer_phone'], 'idx_coupon_redemptions_customer');
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->foreignId('coupon_code_id')->nullable()->after('amount')->constrained('coupon_codes')->nullOnDelete();
            $table->decimal('original_amount', 12, 2)->nullable()->after('coupon_code_id');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('original_amount');
            $table->string('coupon_code')->nullable()->after('discount_amount');
            $table->json('coupon_snapshot')->nullable()->after('coupon_code');
        });

        DB::table('course_enrollments')->update([
            'original_amount' => DB::raw('amount'),
            'discount_amount' => 0,
        ]);
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_code_id');
            $table->dropColumn([
                'original_amount',
                'discount_amount',
                'coupon_code',
                'coupon_snapshot',
            ]);
        });

        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('coupon_code_versions');
        Schema::dropIfExists('coupon_codes');
    }
};
