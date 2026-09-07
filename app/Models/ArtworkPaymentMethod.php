<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtworkPaymentMethod extends BaseModel
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

    public function sales(): HasMany
    {
        return $this->hasMany(ArtworkSale::class, 'payment_method_id');
    }
}
