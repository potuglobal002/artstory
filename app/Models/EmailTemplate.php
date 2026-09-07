<?php

namespace App\Models;

class EmailTemplate extends BaseModel
{
    protected $fillable = [
        'name',
        'key',
        'module',
        'subject',
        'body_html',
        'body_text',
        'available_variables',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'available_variables' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public static function activeTemplate(string $key): ?self
    {
        return static::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();
    }
}
