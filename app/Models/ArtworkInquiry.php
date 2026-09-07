<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtworkInquiry extends BaseModel
{
    protected $fillable = [
        'artwork_id', 'artwork_code', 'artwork_title', 'artist_name',
        'name', 'whatsapp', 'email', 'message', 'status', 'admin_notes',
        'contacted_at', 'closed_at', 'source', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function markStatus(string $status): void
    {
        $this->status = $status;
        $this->contacted_at = $status === 'contacted' && ! $this->contacted_at ? now() : $this->contacted_at;
        $this->closed_at = $status === 'closed' && ! $this->closed_at ? now() : ($status !== 'closed' ? null : $this->closed_at);
    }
}
