<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Event extends BaseModel
{
    protected $fillable = [
        'name', 'slug', 'content_type', 'excerpt', 'details', 'background_image_path', 'start_date', 'end_date',
        'location', 'external_url', 'gallery_images', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'gallery_images' => 'array', 'is_active' => 'boolean'];
    }

    public function backgroundImageUrl(): ?string
    {
        return $this->background_image_path ? Storage::disk('public')->url($this->background_image_path) : null;
    }

    public function galleryImageUrls(): array
    {
        return collect($this->gallery_images ?: [])->map(fn ($path) => Storage::disk('public')->url($path))->all();
    }

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            $model->slug = $model->slug ?: Str::slug($model->name);
        });
    }
}
