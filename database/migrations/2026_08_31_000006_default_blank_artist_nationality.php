<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('artists')
            ->whereNull('nationality')
            ->orWhere('nationality', '')
            ->update(['nationality' => 'Bangladeshi']);

        Schema::table('artists', function (Blueprint $table) {
            $table->string('nationality')->default('Bangladeshi')->change();
        });
    }

    public function down(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->string('nationality')->nullable()->default(null)->change();
        });
    }
};
