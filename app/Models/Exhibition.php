<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Exhibition extends BaseModel
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'description', 'background_image_path', 'start_date', 'end_date',
        'embedded_url', 'virtual_gallery_url', 'gallery_images', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'gallery_images' => 'array', 'is_active' => 'boolean'];
    }

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class, 'exhibition_artwork')->withPivot('sort_order')->orderBy('exhibition_artwork.sort_order');
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
            $model->slug = $model->slug ?: Str::slug($model->title);
        });
    }
}
