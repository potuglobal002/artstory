<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_galleries', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('virtual_gallery_rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('virtual_gallery_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('panorama_path')->nullable();
            $table->string('wall_color', 16)->default('#e8e3d8');
            $table->string('floor_color', 16)->default('#4b4038');
            $table->string('ceiling_color', 16)->default('#f7f5f0');
            $table->decimal('width', 7, 2)->default(12);
            $table->decimal('depth', 7, 2)->default(10);
            $table->decimal('height', 7, 2)->default(5);
            $table->decimal('camera_x', 7, 2)->default(0);
            $table->decimal('camera_y', 7, 2)->default(1.7);
            $table->decimal('camera_z', 7, 2)->default(3.8);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['virtual_gallery_id', 'is_active', 'sort_order'], 'idx_virtual_gallery_rooms_active');
        });

        Schema::create('virtual_gallery_artworks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('virtual_gallery_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->string('wall', 12)->default('back');
            $table->decimal('offset_x', 7, 2)->default(0);
            $table->decimal('offset_y', 7, 2)->default(2.5);
            $table->decimal('display_width', 7, 2)->default(2.2);
            $table->decimal('display_height', 7, 2)->nullable();
            $table->decimal('rotation_y', 7, 2)->default(0);
            $table->string('frame_color', 16)->default('#1a1612');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['virtual_gallery_room_id', 'artwork_id'], 'virtual_gallery_room_artwork_unique');
            $table->index(['virtual_gallery_room_id', 'wall', 'is_active'], 'idx_virtual_gallery_artworks_wall');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_gallery_artworks');
        Schema::dropIfExists('virtual_gallery_rooms');
        Schema::dropIfExists('virtual_galleries');
    }
};
