<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class JourneyItem extends BaseModel
{
    protected $fillable = [
        'year',
        'title',
        'description',
        'image_path',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('year');
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return str_starts_with($this->image_path, 'landing-assets/')
            ? asset($this->image_path)
            : Storage::disk('public')->url($this->image_path);
    }
}
