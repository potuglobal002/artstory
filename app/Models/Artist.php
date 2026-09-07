<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Artist extends BaseModel
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'picture_path',
        'date_of_birth',
        'date_of_death',
        'nationality',
        'birth_place',
        'biography',
        'approval_status',
        'approved_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_of_death' => 'date',
            'approved_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $artist): void {
            $artist->nationality = filled($artist->nationality)
                ? trim((string) $artist->nationality)
                : 'Bangladeshi';
        });
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pictureUrl(): ?string
    {
        return $this->picture_path ? Storage::disk('public')->url($this->picture_path) : null;
    }
}
