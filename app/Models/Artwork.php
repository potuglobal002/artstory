<?php

namespace App\Models;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Artwork extends BaseModel
{
    protected $fillable = [
        'artist_id',
        'artwork_code',
        'artwork_style_id',
        'artwork_subject_style_id',
        'artwork_medium_id',
        'artwork_size_id',
        'artist_style_name',
        'artist_subject_style_name',
        'artist_medium_name',
        'artist_size_name',
        'title',
        'image_path',
        'canvas_count',
        'gallery_images',
        'year',
        'price',
        'selling_price',
        'usd_price',
        'usd_selling_price',
        'previous_price',
        'previous_selling_price',
        'previous_usd_price',
        'previous_usd_selling_price',
        'price_version',
        'status',
        'note',
        'submitted_by_artist',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'canvas_count' => 'integer',
            'gallery_images' => 'array',
            'price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'usd_price' => 'decimal:2',
            'usd_selling_price' => 'decimal:2',
            'previous_price' => 'decimal:2',
            'previous_selling_price' => 'decimal:2',
            'previous_usd_price' => 'decimal:2',
            'previous_usd_selling_price' => 'decimal:2',
            'price_version' => 'integer',
            'submitted_by_artist' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $artwork): void {
            $artwork->title = $artwork->normalizedTitle();
            $artwork->canvas_count = max(1, (int) ($artwork->canvas_count ?: 1));
            $artwork->validateData();
            $artwork->artwork_code ??= $artwork->makeArtworkCode();
            $artwork->price_version ??= 1;
        });

        static::updating(function (self $artwork): void {
            $artwork->title = $artwork->normalizedTitle();
            $artwork->canvas_count = max(1, (int) ($artwork->canvas_count ?: 1));
            $artwork->validateData();

            if (! filled($artwork->artwork_code)) {
                $artwork->artwork_code = $artwork->makeArtworkCode();
            }

            if (! $artwork->hasPriceVersionedChanges()) {
                return;
            }

            $artwork->priceVersions()->create([
                'price' => $artwork->getOriginal('price'),
                'selling_price' => $artwork->getOriginal('selling_price'),
                'usd_price' => $artwork->getOriginal('usd_price'),
                'usd_selling_price' => $artwork->getOriginal('usd_selling_price'),
                'status' => $artwork->getOriginal('status') ?: 'available',
                'version' => $artwork->getOriginal('price_version') ?: 1,
                'effective_until' => now(),
                'changed_by_id' => auth()->id(),
                'note' => $artwork->getOriginal('note'),
            ]);

            $artwork->previous_price = $artwork->getOriginal('price');
            $artwork->previous_selling_price = $artwork->getOriginal('selling_price');
            $artwork->previous_usd_price = $artwork->getOriginal('usd_price');
            $artwork->previous_usd_selling_price = $artwork->getOriginal('usd_selling_price');
            $artwork->price_version = ((int) $artwork->getOriginal('price_version')) + 1;
        });
    }

    public function save(array $options = []): bool
    {
        return app(DatabaseManager::class)->transaction(fn (): bool => parent::save($options));
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(ArtworkStyle::class, 'artwork_style_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(ArtworkSize::class, 'artwork_size_id');
    }

    public function subjectStyle(): BelongsTo
    {
        return $this->belongsTo(ArtworkSubjectStyle::class, 'artwork_subject_style_id');
    }

    public function medium(): BelongsTo
    {
        return $this->belongsTo(ArtworkMedium::class, 'artwork_medium_id');
    }

    public function priceVersions(): HasMany
    {
        return $this->hasMany(ArtworkPriceVersion::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(ArtworkSale::class);
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public function galleryImageUrls(): array
    {
        return collect($this->gallery_images ?: [])
            ->filter()
            ->map(fn (string $path): string => Storage::disk('public')->url($path))
            ->prepend($this->imageUrl())
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function scanUrl(): string
    {
        return route('artworks.scan', $this->artwork_code);
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function displayPrice(string $currency = 'BDT'): ?float
    {
        $fields = strtoupper($currency) === 'USD'
            ? ['usd_selling_price', 'usd_price']
            : ['selling_price', 'price'];

        foreach ($fields as $field) {
            if (! blank($this->{$field})) {
                return (float) $this->{$field};
            }
        }

        return null;
    }

    private function normalizedTitle(): string
    {
        return filled($this->title) ? trim((string) $this->title) : 'Untitled';
    }

    private function makeArtworkCode(): string
    {
        $prefix = $this->artistInitials();
        $sequence = 1;

        $latestCode = static::query()
            ->where('artist_id', $this->artist_id)
            ->where('artwork_code', 'like', $prefix.'-P-%')
            ->orderByDesc('id')
            ->value('artwork_code');

        if ($latestCode && preg_match('/-(\d+)$/', $latestCode, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        do {
            $code = $prefix.'-P-'.str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
            $sequence++;
        } while (static::query()->where('artwork_code', $code)->exists());

        return $code;
    }

    private function artistInitials(): string
    {
        $name = $this->artist?->name
            ?: Artist::query()->whereKey($this->artist_id)->value('name')
            ?: 'Artwork';

        $initials = Str::of($name)
            ->replaceMatches('/[^A-Za-z0-9\s]/', ' ')
            ->squish()
            ->explode(' ')
            ->filter()
            ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');

        return $initials !== '' ? Str::substr($initials, 0, 4) : 'ART';
    }

    private function hasPriceVersionedChanges(): bool
    {
        return $this->isDirty([
            'price',
            'selling_price',
            'usd_price',
            'usd_selling_price',
            'status',
        ]);
    }

    private function validateData(): void
    {
        if ((int) $this->canvas_count < 1) {
            throw new \InvalidArgumentException('An artwork must have at least one canvas.');
        }

        foreach (['price', 'selling_price', 'usd_price', 'usd_selling_price'] as $field) {
            if ($this->{$field} !== null && (float) $this->{$field} < 0) {
                throw new \InvalidArgumentException("Artwork {$field} cannot be negative.");
            }
        }
    }
}
