<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_galleries', function (Blueprint $table): void {
            $table->boolean('automatic_use_realistic_environment')->default(true)->after('automatic_show_floor_grid');
            $table->string('automatic_environment_path')->nullable()->after('automatic_use_realistic_environment');
        });
    }

    public function down(): void
    {
        Schema::table('virtual_galleries', function (Blueprint $table): void {
            $table->dropColumn([
                'automatic_use_realistic_environment',
                'automatic_environment_path',
            ]);
        });
    }
};
