<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('landing_pages')) {
            return;
        }

        Schema::table('landing_pages', function (Blueprint $table): void {
            if (! Schema::hasColumn('landing_pages', 'about_image_path')) {
                $table->string('about_image_path')->nullable()->after('hero_background_path');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('landing_pages')) {
            return;
        }

        Schema::table('landing_pages', function (Blueprint $table): void {
            if (Schema::hasColumn('landing_pages', 'about_image_path')) {
                $table->dropColumn('about_image_path');
            }
        });
    }
};
