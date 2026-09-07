<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_galleries', function (Blueprint $table): void {
            $table->boolean('auto_include_available_artworks')->default(true)->after('cover_image_path');
            $table->unsignedSmallInteger('artworks_per_room')->default(18)->after('auto_include_available_artworks');
        });
    }

    public function down(): void
    {
        Schema::table('virtual_galleries', function (Blueprint $table): void {
            $table->dropColumn(['auto_include_available_artworks', 'artworks_per_room']);
        });
    }
};
