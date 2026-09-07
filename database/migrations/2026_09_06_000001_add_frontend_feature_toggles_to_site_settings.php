<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->boolean('virtual_gallery_enabled')->default(true)->after('address');
            $table->boolean('artist_login_enabled')->default(true)->after('virtual_gallery_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn(['virtual_gallery_enabled', 'artist_login_enabled']);
        });
    }
};
