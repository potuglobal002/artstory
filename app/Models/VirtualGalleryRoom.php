<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class VirtualGalleryRoom extends BaseModel
{
    protected $fillable = [
        'virtual_gallery_id',
        'name',
        'description',
        'panorama_path',
        'wall_color',
        'floor_color',
        'ceiling_color',
        'width',
        'depth',
        'height',
        'camera_x',
        'camera_y',
        'camera_z',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'decimal:2',
            'depth' => 'decimal:2',
            'height' => 'decimal:2',
            'camera_x' => 'decimal:2',
            'camera_y' => 'decimal:2',
            'camera_z' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(VirtualGallery::class, 'virtual_gallery_id');
    }

    public function placements(): HasMany
    {
        return $this->hasMany(VirtualGalleryArtwork::class)->orderBy('sort_order')->orderBy('id');
    }

    public function panoramaUrl(): ?string
    {
        return $this->panorama_path ? Storage::disk('public')->url($this->panorama_path) : null;
    }
}
