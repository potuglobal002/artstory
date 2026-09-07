<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_galleries', function (Blueprint $table): void {
            $table->string('automatic_wall_color', 16)->default('#e4e5e7')->after('artworks_per_room');
            $table->string('automatic_floor_color', 16)->default('#b5b7b9')->after('automatic_wall_color');
            $table->string('automatic_ceiling_color', 16)->default('#62656c')->after('automatic_floor_color');
            $table->decimal('automatic_room_width', 7, 2)->default(28)->after('automatic_ceiling_color');
            $table->decimal('automatic_room_depth', 7, 2)->default(26)->after('automatic_room_width');
            $table->decimal('automatic_room_height', 7, 2)->default(7.2)->after('automatic_room_depth');
            $table->decimal('automatic_camera_x', 7, 2)->default(0)->after('automatic_room_height');
            $table->decimal('automatic_camera_y', 7, 2)->default(2.05)->after('automatic_camera_x');
            $table->decimal('automatic_camera_z', 7, 2)->default(10.5)->after('automatic_camera_y');
            $table->boolean('automatic_show_partitions')->default(true)->after('automatic_camera_z');
            $table->boolean('automatic_show_floor_grid')->default(true)->after('automatic_show_partitions');
        });
    }

    public function down(): void
    {
        Schema::table('virtual_galleries', function (Blueprint $table): void {
            $table->dropColumn([
                'automatic_wall_color',
                'automatic_floor_color',
                'automatic_ceiling_color',
                'automatic_room_width',
                'automatic_room_depth',
                'automatic_room_height',
                'automatic_camera_x',
                'automatic_camera_y',
                'automatic_camera_z',
                'automatic_show_partitions',
                'automatic_show_floor_grid',
            ]);
        });
    }
};
