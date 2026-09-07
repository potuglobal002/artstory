<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('mail_settings', 'timeout')) {
            return;
        }

        Schema::table('mail_settings', function (Blueprint $table) {
            $table->unsignedInteger('timeout')->default(8)->after('ehlo_domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('mail_settings', 'timeout')) {
            return;
        }

        Schema::table('mail_settings', function (Blueprint $table) {
            $table->dropColumn('timeout');
        });
    }
};
