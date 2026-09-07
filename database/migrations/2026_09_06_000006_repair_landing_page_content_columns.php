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

        $columns = [
            'featured_artwork_ids' => fn (Blueprint $table) => $table->json('featured_artwork_ids')->nullable(),
            'featured_artist_ids' => fn (Blueprint $table) => $table->json('featured_artist_ids')->nullable(),
            'exhibition_section' => fn (Blueprint $table) => $table->json('exhibition_section')->nullable(),
            'exhibition_image_path' => fn (Blueprint $table) => $table->string('exhibition_image_path')->nullable(),
            'event_section' => fn (Blueprint $table) => $table->json('event_section')->nullable(),
        ];

        foreach ($columns as $column => $definition) {
            if (Schema::hasColumn('landing_pages', $column)) {
                continue;
            }

            Schema::table('landing_pages', $definition);
        }
    }

    public function down(): void
    {
        // This migration only repairs columns that an earlier migration intended to add.
    }
};
