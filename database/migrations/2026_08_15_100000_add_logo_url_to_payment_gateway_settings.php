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
        Schema::table('payment_gateway_settings', function (Blueprint $table) {
            $table->string('logo_url')->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        // Old module removed from this ART Story admin build.
        return;
        Schema::table('payment_gateway_settings', function (Blueprint $table) {
            $table->dropColumn('logo_url');
        });
    }
};
