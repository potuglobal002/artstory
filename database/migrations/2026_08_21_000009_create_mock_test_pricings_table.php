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
        Schema::create('mock_test_pricings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('mock_test_count')->unique();
            $table->string('title');
            $table->decimal('price_amount', 12, 2);
            $table->string('currency', 3)->default('BDT');
            $table->unsignedInteger('version')->default(1);
            $table->decimal('previous_price_amount', 12, 2)->nullable();
            $table->date('effective_from')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'mock_test_count'], 'idx_mock_test_pricings_active_count');
            $table->index(['version', 'effective_from'], 'idx_mock_test_pricings_version_effective');
        });

        Schema::create('mock_test_pricing_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_test_pricing_id')->constrained('mock_test_pricings')->cascadeOnDelete();
            $table->unsignedTinyInteger('mock_test_count');
            $table->string('title');
            $table->decimal('price_amount', 12, 2);
            $table->string('currency', 3)->default('BDT');
            $table->unsignedInteger('version');
            $table->date('effective_from')->nullable();
            $table->timestamp('effective_until')->nullable();
            $table->foreignId('changed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['mock_test_pricing_id', 'version'], 'idx_mock_test_pricing_versions_record_version');
            $table->index(['mock_test_count', 'effective_from'], 'idx_mock_test_pricing_versions_count_effective');
        });

        DB::table('mock_test_pricings')->insert([
            [
                'mock_test_count' => 1,
                'title' => '1 Mock Test',
                'price_amount' => 1500,
                'currency' => 'BDT',
                'effective_from' => now()->toDateString(),
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mock_test_count' => 2,
                'title' => '2 Mock Tests',
                'price_amount' => 2800,
                'currency' => 'BDT',
                'effective_from' => now()->toDateString(),
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mock_test_count' => 3,
                'title' => '3 Mock Tests',
                'price_amount' => 3600,
                'currency' => 'BDT',
                'effective_from' => now()->toDateString(),
                'sort_order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mock_test_count' => 4,
                'title' => '4 Mock Tests',
                'price_amount' => 4600,
                'currency' => 'BDT',
                'effective_from' => now()->toDateString(),
                'sort_order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mock_test_count' => 5,
                'title' => '5 Mock Tests',
                'price_amount' => 5000,
                'currency' => 'BDT',
                'effective_from' => now()->toDateString(),
                'sort_order' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::dropIfExists('mock_test_pricing_versions');
        Schema::dropIfExists('mock_test_pricings');
    }
};
