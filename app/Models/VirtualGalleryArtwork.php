<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualGalleryArtwork extends BaseModel
{
    protected $fillable = [
        'virtual_gallery_room_id',
        'artwork_id',
        'wall',
        'offset_x',
        'offset_y',
        'display_width',
        'display_height',
        'rotation_y',
        'frame_color',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'offset_x' => 'decimal:2',
            'offset_y' => 'decimal:2',
            'display_width' => 'decimal:2',
            'display_height' => 'decimal:2',
            'rotation_y' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(VirtualGalleryRoom::class, 'virtual_gallery_room_id');
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}
