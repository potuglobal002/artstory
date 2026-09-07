<?php

namespace Database\Seeders;

use App\Models\Artwork;
use App\Models\VirtualGallery;
use App\Models\VirtualGalleryRoom;
use Illuminate\Database\Seeder;

class VirtualGallerySeeder extends Seeder
{
    public function run(): void
    {
        $gallery = VirtualGallery::query()->firstOrCreate(
            ['slug' => 'art-story-virtual-gallery'],
            [
                'name' => 'ART Story Virtual Gallery',
                'description' => 'A curated digital walk-through of selected ART Story artworks.',
                'auto_include_available_artworks' => true,
                'artworks_per_room' => 18,
                'is_active' => true,
                'sort_order' => 0,
            ],
        );

        $gallery->update([
            'auto_include_available_artworks' => true,
            'artworks_per_room' => 18,
        ]);

        $room = VirtualGalleryRoom::query()->firstOrCreate(
            [
                'virtual_gallery_id' => $gallery->id,
                'name' => 'Collection Room',
            ],
            [
                'description' => 'A selection from the ART Story collection.',
                'wall_color' => '#e8e3d8',
                'floor_color' => '#4b4038',
                'ceiling_color' => '#f7f5f0',
                'width' => 12,
                'depth' => 10,
                'height' => 5,
                'camera_x' => 0,
                'camera_y' => 1.7,
                'camera_z' => 3.8,
                'is_active' => true,
                'sort_order' => 0,
            ],
        );

        if ($room->placements()->exists()) {
            return;
        }

        $positions = [
            ['back', -3.2, 2.5],
            ['back', 0, 2.5],
            ['back', 3.2, 2.5],
            ['left', -2.3, 2.5],
            ['left', 2.3, 2.5],
            ['right', -2.3, 2.5],
            ['right', 2.3, 2.5],
            ['front', 0, 2.5],
        ];

        Artwork::query()
            ->where('is_active', true)
            ->where('status', 'available')
            ->orderBy('id')
            ->limit(count($positions))
            ->get()
            ->each(function (Artwork $artwork, int $index) use ($room, $positions): void {
                [$wall, $offsetX, $offsetY] = $positions[$index];

                $room->placements()->create([
                    'artwork_id' => $artwork->id,
                    'wall' => $wall,
                    'offset_x' => $offsetX,
                    'offset_y' => $offsetY,
                    'display_width' => 2.1,
                    'frame_color' => '#1a1612',
                    'is_active' => true,
                    'sort_order' => $index,
                ]);
            });
    }
}
