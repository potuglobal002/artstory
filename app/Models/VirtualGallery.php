<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VirtualGallery extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image_path',
        'auto_include_available_artworks',
        'artworks_per_room',
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
        'automatic_use_realistic_environment',
        'automatic_environment_path',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'auto_include_available_artworks' => 'boolean',
            'artworks_per_room' => 'integer',
            'automatic_room_width' => 'decimal:2',
            'automatic_room_depth' => 'decimal:2',
            'automatic_room_height' => 'decimal:2',
            'automatic_camera_x' => 'decimal:2',
            'automatic_camera_y' => 'decimal:2',
            'automatic_camera_z' => 'decimal:2',
            'automatic_show_partitions' => 'boolean',
            'automatic_show_floor_grid' => 'boolean',
            'automatic_use_realistic_environment' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $gallery): void {
            $gallery->slug = Str::slug($gallery->slug ?: $gallery->name);
        });
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(VirtualGalleryRoom::class)->orderBy('sort_order')->orderBy('id');
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image_path ? Storage::disk('public')->url($this->cover_image_path) : null;
    }

    public function automaticEnvironmentUrl(): ?string
    {
        if ($this->automatic_use_realistic_environment === false) {
            return null;
        }

        return $this->automatic_environment_path
            ? Storage::disk('public')->url($this->automatic_environment_path)
            : asset('images/virtual-gallery-environment.png');
    }
}
