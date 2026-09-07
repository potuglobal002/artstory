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
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('picture_path')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_death')->nullable();
            $table->string('nationality')->default('Bangladeshi');
            $table->string('birth_place')->nullable();
            $table->text('biography')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('artwork_styles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('artwork_subject_styles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('artwork_mediums', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('artwork_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->string('unit', 20)->default('cm');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('artwork_style_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('artwork_subject_style_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('artwork_medium_id')->nullable()->constrained('artwork_mediums')->nullOnDelete();
            $table->foreignId('artwork_size_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->default('Untitled');
            $table->string('image_path')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->decimal('previous_price', 12, 2)->nullable();
            $table->decimal('previous_selling_price', 12, 2)->nullable();
            $table->unsignedInteger('price_version')->default(1);
            $table->string('status', 30)->default('available');
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['artist_id', 'year']);
        });

        Schema::create('artwork_price_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained('artworks')->cascadeOnDelete();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->string('status', 30)->default('available');
            $table->unsignedInteger('version');
            $table->timestamp('effective_until')->nullable();
            $table->foreignId('changed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['artwork_id', 'version'], 'idx_artwork_price_versions_record_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_price_versions');
        Schema::dropIfExists('artworks');
        Schema::dropIfExists('artwork_sizes');
        Schema::dropIfExists('artwork_mediums');
        Schema::dropIfExists('artwork_subject_styles');
        Schema::dropIfExists('artwork_styles');
        Schema::dropIfExists('artists');
    }
};
