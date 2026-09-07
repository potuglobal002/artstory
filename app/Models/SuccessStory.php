<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuccessStory extends BaseModel
{
    protected $fillable = [
        'name',
        'slug',
        'course',
        'result',
        'meta',
        'quote',
        'image_path',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $story): void {
            $story->slug = $story->slug ?: Str::slug($story->name);
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderByDesc('created_at');
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
