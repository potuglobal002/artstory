<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtworkEmailLog extends BaseModel
{
    protected $fillable = [
        'artwork_sale_id',
        'artwork_inquiry_id',
        'template_key',
        'recipient_email',
        'subject',
        'status',
        'payload',
        'error_message',
        'sent_at',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(ArtworkSale::class, 'artwork_sale_id');
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(ArtworkInquiry::class, 'artwork_inquiry_id');
    }
}
