<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;

class PaymentGatewaySetting extends BaseModel
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
        'is_sandbox',
        'use_sandbox_simulator',
        'currency',
        'logo_url',
        'merchant_id',
        'store_id',
        'username',
        'password',
        'api_key',
        'api_secret',
        'base_url',
        'checkout_url',
        'extra_config',
        'notes',
        'sort_order',
    ];

    protected $hidden = [
        'password',
        'api_key',
        'api_secret',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_sandbox' => 'boolean',
            'use_sandbox_simulator' => 'boolean',
            'extra_config' => 'array',
            'password' => 'encrypted',
            'api_key' => 'encrypted',
            'api_secret' => 'encrypted',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Payment Gateway')
            ->logExcept(['password', 'api_key', 'api_secret', 'created_at', 'updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
