<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtworkMedium extends BaseModel
{
    protected $table = 'artwork_mediums';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class, 'artwork_medium_id');
    }
}
