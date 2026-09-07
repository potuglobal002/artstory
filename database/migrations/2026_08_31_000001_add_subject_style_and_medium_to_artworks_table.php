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
        if (! Schema::hasTable('artwork_subject_styles')) {
            Schema::create('artwork_subject_styles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('artwork_mediums')) {
            Schema::create('artwork_mediums', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        Schema::table('artworks', function (Blueprint $table) {
            if (! Schema::hasColumn('artworks', 'artwork_subject_style_id')) {
                $table->foreignId('artwork_subject_style_id')
                    ->nullable()
                    ->after('artwork_style_id')
                    ->constrained('artwork_subject_styles')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('artworks', 'artwork_medium_id')) {
                $table->foreignId('artwork_medium_id')
                    ->nullable()
                    ->after('artwork_subject_style_id')
                    ->constrained('artwork_mediums')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (Schema::hasColumn('artworks', 'artwork_medium_id')) {
                $table->dropConstrainedForeignId('artwork_medium_id');
            }

            if (Schema::hasColumn('artworks', 'artwork_subject_style_id')) {
                $table->dropConstrainedForeignId('artwork_subject_style_id');
            }
        });

        Schema::dropIfExists('artwork_mediums');
        Schema::dropIfExists('artwork_subject_styles');
    }
};
