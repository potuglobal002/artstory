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
            if (! Schema::hasColumn('landing_pages', 'featured_artwork_ids')) {
                $table->json('featured_artwork_ids')->nullable()->after('programs');
            }

            if (! Schema::hasColumn('landing_pages', 'featured_artist_ids')) {
                $table->json('featured_artist_ids')->nullable()->after('featured_artwork_ids');
            }

            if (! Schema::hasColumn('landing_pages', 'exhibition_section')) {
                $table->json('exhibition_section')->nullable()->after('featured_artist_ids');
            }

            if (! Schema::hasColumn('landing_pages', 'exhibition_image_path')) {
                $table->string('exhibition_image_path')->nullable()->after('exhibition_section');
            }

            if (! Schema::hasColumn('landing_pages', 'event_section')) {
                $table->json('event_section')->nullable()->after('exhibition_image_path');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('landing_pages')) {
            return;
        }

        Schema::table('landing_pages', function (Blueprint $table): void {
            foreach (['event_section', 'exhibition_image_path', 'exhibition_section', 'featured_artist_ids', 'featured_artwork_ids'] as $column) {
                if (Schema::hasColumn('landing_pages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
