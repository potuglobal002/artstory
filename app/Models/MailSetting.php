<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;

class MailSetting extends BaseModel
{
    protected $fillable = [
        'is_active',
        'mailer',
        'host',
        'port',
        'scheme',
        'username',
        'password',
        'from_address',
        'from_name',
        'ehlo_domain',
        'timeout',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'port' => 'integer',
            'timeout' => 'integer',
            'password' => 'encrypted',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Email Settings')
            ->logExcept(['password', 'created_at', 'updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'is_active' => true,
            'mailer' => env('MAIL_MAILER', 'smtp'),
            'host' => env('MAIL_HOST', 'smtp.hostinger.com'),
            'port' => (int) env('MAIL_PORT', 465),
            'scheme' => env('MAIL_SCHEME', 'smtps'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'from_address' => env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME', 'hello@example.com')),
            'from_name' => env('MAIL_FROM_NAME', config('app.name')),
            'ehlo_domain' => env('MAIL_EHLO_DOMAIN'),
            'timeout' => (int) env('MAIL_TIMEOUT', 8),
        ]);
    }
}
