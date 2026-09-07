<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Course extends BaseModel
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'badge',
        'excerpt',
        'description',
        'who_for',
        'lead',
        'image_path',
        'duration',
        'sessions',
        'fee',
        'fee_amount',
        'fee_note',
        'target_score',
        'batch_size',
        'campus',
        'features',
        'outcomes',
        'includes',
        'modules',
        'schedule',
        'faq',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'outcomes' => 'array',
            'includes' => 'array',
            'modules' => 'array',
            'schedule' => 'array',
            'faq' => 'array',
            'fee_amount' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $course): void {
            $course->slug = $course->slug ?: Str::slug($course->title);
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('title');
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

    public function payableAmount(): float
    {
        if ($this->fee_amount !== null) {
            return (float) $this->fee_amount;
        }

        return (float) preg_replace('/[^0-9.]/', '', (string) $this->fee);
    }

    public function feeLabel(): string
    {
        $amount = $this->payableAmount();

        if ($amount > 0) {
            return '৳ ' . number_format($amount, 0);
        }

        return $this->fee ?: 'Course details';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
