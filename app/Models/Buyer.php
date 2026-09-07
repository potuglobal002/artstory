<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Buyer extends BaseModel
{
    protected $fillable = [
        'name',
        'designation',
        'phone',
        'email',
        'address',
        'note',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(ArtworkSale::class);
    }

    public function label(): string
    {
        return collect([$this->name, $this->designation])
            ->filter()
            ->implode(' - ');
    }
}
