<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtworkSize extends BaseModel
{
    protected $fillable = [
        'name',
        'width',
        'height',
        'unit',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class);
    }

    public function label(): string
    {
        if ($this->width && $this->height) {
            return "{$this->name} ({$this->width} x {$this->height} {$this->unit})";
        }

        return $this->name;
    }
}
