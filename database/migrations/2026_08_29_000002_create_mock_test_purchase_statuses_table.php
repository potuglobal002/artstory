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
        Schema::create('mock_test_purchase_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('mock_test_purchase_statuses')->insert([
            ['code' => 'student_after_reg', 'label' => 'Student (After Reg)', 'sort_order' => 10, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'student_before_reg', 'label' => 'Student (Before Reg)', 'sort_order' => 20, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'free_with_reg_1', 'label' => 'Free with Reg (1)', 'sort_order' => 30, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'free_with_reg_2', 'label' => 'Free with Reg (2)', 'sort_order' => 40, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'purchase_1_mock', 'label' => 'Purchase - 1 Mock', 'sort_order' => 50, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'purchase_2_mocks', 'label' => 'Purchase - 2 Mocks', 'sort_order' => 60, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'purchase_3_mocks', 'label' => 'Purchase - 3 Mocks', 'sort_order' => 70, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'purchase_4_mocks', 'label' => 'Purchase - 4 Mocks', 'sort_order' => 80, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'purchase_5_mocks', 'label' => 'Purchase - 5 Mocks', 'sort_order' => 90, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'buy_1_get_1', 'label' => 'Buy 1 Get 1', 'sort_order' => 100, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'crash_program_3', 'label' => 'Crash Program (3)', 'sort_order' => 110, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::dropIfExists('mock_test_purchase_statuses');
    }
};
