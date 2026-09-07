<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtworkSubjectStyle extends BaseModel
{
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
        return $this->hasMany(Artwork::class);
    }
}
