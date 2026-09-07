<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtworkPriceVersion extends BaseModel
{
    protected $fillable = [
        'artwork_id',
        'price',
        'selling_price',
        'usd_price',
        'usd_selling_price',
        'status',
        'version',
        'effective_until',
        'changed_by_id',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'usd_price' => 'decimal:2',
            'usd_selling_price' => 'decimal:2',
            'version' => 'integer',
            'effective_until' => 'datetime',
        ];
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_id');
    }
}
