<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\VirtualGallery;
use App\Support\SiteSettings;
use Illuminate\Support\Collection;

class VirtualGalleryController extends Controller
{
    public function index()
    {
        return view('artstory.virtual-galleries.index', [
            'siteSettings' => SiteSettings::current(),
            'galleries' => VirtualGallery::query()
                ->where('is_active', true)
                ->withCount(['rooms' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(VirtualGallery $gallery)
    {
        abort_unless($gallery->is_active, 404);

        $gallery->load([
            'rooms' => fn ($query) => $query
                ->where('is_active', true)
                ->with([
                    'placements' => fn ($placements) => $placements
                        ->where('is_active', true)
                        ->with(['artwork.artist', 'artwork.medium', 'artwork.size'])
                        ->orderBy('sort_order')
                        ->orderBy('id'),
                ]),
        ]);

        $rooms = $gallery->auto_include_available_artworks
            ? $this->automaticRooms($gallery)
            : $this->manualRooms($gallery);

        abort_if($rooms === [], 404);

        return view('artstory.virtual-galleries.show', [
            'siteSettings' => SiteSettings::current(),
            'gallery' => $gallery,
            'scene' => [
                'name' => $gallery->name,
                'rooms' => $rooms,
            ],
        ]);
    }

    private function automaticRooms(VirtualGallery $gallery): array
    {
        $artworks = Artwork::query()
            ->where('is_active', true)
            ->where('status', 'available')
            ->whereHas('artist', fn ($query) => $query->where('is_active', true))
            ->with(['artist', 'medium', 'size'])
            ->orderBy('artist_id')
            ->orderBy('title')
            ->get();

        return $artworks
            ->chunk(max(6, min(30, $gallery->artworks_per_room ?: 18)))
            ->values()
            ->map(fn (Collection $artworks, int $index): array => [
                'id' => 'automatic-'.$index,
                'name' => 'Gallery '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'description' => sprintf('%d available artworks', $artworks->count()),
                'panoramaUrl' => null,
                'environmentUrl' => $gallery->automaticEnvironmentUrl(),
                'wallColor' => $gallery->automatic_wall_color,
                'floorColor' => $gallery->automatic_floor_color,
                'ceilingColor' => $gallery->automatic_ceiling_color,
                'width' => (float) $gallery->automatic_room_width,
                'depth' => (float) $gallery->automatic_room_depth,
                'height' => (float) $gallery->automatic_room_height,
                'camera' => [(float) $gallery->automatic_camera_x, (float) $gallery->automatic_camera_y, (float) $gallery->automatic_camera_z],
                'showPartitions' => $gallery->automatic_show_partitions,
                'showFloorGrid' => $gallery->automatic_show_floor_grid,
                'artworks' => $this->automaticPlacements($artworks, $gallery),
            ])
            ->all();
    }

    private function automaticPlacements(Collection $artworks, VirtualGallery $gallery): array
    {
        $walls = ['back', 'front', 'left', 'right', 'divider-left', 'divider-right'];
        $wallCounts = array_fill_keys($walls, 0);

        foreach ($artworks as $index => $artwork) {
            $wallCounts[$walls[$index % count($walls)]]++;
        }

        $wallIndexes = array_fill_keys($walls, 0);

        return $artworks->values()->map(function (Artwork $artwork, int $index) use (&$wallIndexes, $gallery, $wallCounts, $walls): array {
            $wall = $walls[$index % count($walls)];
            $position = $wallIndexes[$wall]++;
            $count = $wallCounts[$wall];
            $span = match ($wall) {
                'back', 'front' => (float) $gallery->automatic_room_width * 0.8,
                'left', 'right' => (float) $gallery->automatic_room_depth * 0.74,
                default => (float) $gallery->automatic_room_depth * 0.29,
            };
            $spacing = $count > 1 ? min(3.9, $span / ($count - 1)) : 0;
            $offsetX = ($position - (($count - 1) / 2)) * $spacing;

            if (in_array($wall, ['divider-left', 'divider-right'], true)) {
                $offsetX -= (float) $gallery->automatic_room_depth * 0.02;
            }

            return [
                'title' => $artwork->title ?: 'Untitled',
                'artist' => $artwork->artist?->name ?: 'Unknown artist',
                'code' => $artwork->artwork_code,
                'medium' => $artwork->medium?->name ?: $artwork->artist_medium_name ?: 'Not specified',
                'dimensions' => $artwork->size?->name ?: $artwork->artist_size_name ?: 'Not specified',
                'year' => $artwork->year ? (string) $artwork->year : 'Not specified',
                'imageUrl' => $artwork->imageUrl(),
                'url' => route('artworks.show', $artwork),
                'wall' => $wall,
                'offsetX' => round($offsetX, 2),
                'offsetY' => 3.25,
                'width' => in_array($wall, ['back', 'front'], true) ? 2.55 : 2.25,
                'height' => null,
                'rotationY' => 0.0,
                'frameColor' => '#201c18',
            ];
        })->all();
    }

    private function manualRooms(VirtualGallery $gallery): array
    {
        return $gallery->rooms->map(function ($room): array {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'description' => $room->description,
                'panoramaUrl' => $room->panoramaUrl(),
                'environmentUrl' => $room->panoramaUrl(),
                'wallColor' => $room->wall_color,
                'floorColor' => $room->floor_color,
                'ceilingColor' => $room->ceiling_color,
                'width' => (float) $room->width,
                'depth' => (float) $room->depth,
                'height' => (float) $room->height,
                'camera' => [(float) $room->camera_x, (float) $room->camera_y, (float) $room->camera_z],
                'showPartitions' => false,
                'showFloorGrid' => false,
                'artworks' => $room->placements
                    ->filter(fn ($placement): bool => $placement->artwork?->is_active && $placement->artwork?->artist?->is_active)
                    ->map(function ($placement): array {
                        $artwork = $placement->artwork;

                        return [
                            'title' => $artwork->title ?: 'Untitled',
                            'artist' => $artwork->artist?->name ?: 'Unknown artist',
                            'code' => $artwork->artwork_code,
                            'medium' => $artwork->medium?->name ?: $artwork->artist_medium_name ?: 'Not specified',
                            'dimensions' => $artwork->size?->name ?: $artwork->artist_size_name ?: 'Not specified',
                            'year' => $artwork->year ? (string) $artwork->year : 'Not specified',
                            'imageUrl' => $artwork->imageUrl(),
                            'url' => route('artworks.show', $artwork),
                            'wall' => $placement->wall,
                            'offsetX' => (float) $placement->offset_x,
                            'offsetY' => (float) $placement->offset_y,
                            'width' => (float) $placement->display_width,
                            'height' => $placement->display_height ? (float) $placement->display_height : null,
                            'rotationY' => (float) $placement->rotation_y,
                            'frameColor' => $placement->frame_color,
                        ];
                    })
                    ->values()
                    ->all(),
            ];
        })->values()->all();
    }
}
