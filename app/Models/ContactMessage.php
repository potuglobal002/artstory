<?php

namespace App\Models;

class ContactMessage extends BaseModel
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'admin_notes',
        'contacted_at',
        'closed_at',
        'source',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function markStatus(string $status): void
    {
        $this->status = $status;
        $this->contacted_at = $status === 'contacted' && ! $this->contacted_at ? now() : $this->contacted_at;
        $this->closed_at = $status === 'closed' && ! $this->closed_at ? now() : ($status !== 'closed' ? null : $this->closed_at);
    }
}
