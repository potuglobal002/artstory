<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table): void {
            if (! Schema::hasColumn('artworks', 'artist_style_name')) {
                $table->string('artist_style_name')->nullable()->after('artwork_size_id');
            }

            if (! Schema::hasColumn('artworks', 'artist_subject_style_name')) {
                $table->string('artist_subject_style_name')->nullable()->after('artist_style_name');
            }

            if (! Schema::hasColumn('artworks', 'artist_medium_name')) {
                $table->string('artist_medium_name')->nullable()->after('artist_subject_style_name');
            }

            if (! Schema::hasColumn('artworks', 'artist_size_name')) {
                $table->string('artist_size_name')->nullable()->after('artist_medium_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table): void {
            foreach (['artist_size_name', 'artist_medium_name', 'artist_subject_style_name', 'artist_style_name'] as $column) {
                if (Schema::hasColumn('artworks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
