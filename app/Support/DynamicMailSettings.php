<?php

namespace App\Support;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DynamicMailSettings
{
    public static function apply(bool $purgeResolvedMailers = false): void
    {
        try {
            if (! Schema::hasTable('mail_settings')) {
                return;
            }

            $settings = Cache::remember('settings.mail', now()->addMinutes(30), function (): array {
                $settings = MailSetting::current();

                return [
                    'is_active' => $settings->is_active,
                    'mailer' => $settings->mailer,
                    'host' => $settings->host,
                    'port' => $settings->port,
                    'scheme' => $settings->scheme,
                    'username' => $settings->username,
                    'password' => $settings->password,
                    'from_address' => $settings->from_address,
                    'from_name' => $settings->from_name,
                    'ehlo_domain' => $settings->ehlo_domain,
                    'timeout' => $settings->timeout ?: 8,
                ];
            });
        } catch (Throwable) {
            return;
        }

        if (! ($settings['is_active'] ?? false)) {
            return;
        }

        $localDomain = $settings['ehlo_domain']
            ?: str($settings['from_address'] ?? '')->after('@')->value()
            ?: parse_url((string) config('app.url'), PHP_URL_HOST);

        $scheme = $settings['scheme'] ?: ((int) ($settings['port'] ?? 0) === 465 ? 'smtps' : null);

        config([
            'mail.default' => $settings['mailer'] ?: 'smtp',
            'mail.from.address' => $settings['from_address'],
            'mail.from.name' => $settings['from_name'] ?: config('app.name'),
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.mailers.smtp.host' => $settings['host'],
            'mail.mailers.smtp.port' => $settings['port'],
            'mail.mailers.smtp.username' => $settings['username'],
            'mail.mailers.smtp.password' => $settings['password'],
            'mail.mailers.smtp.timeout' => $settings['timeout'] ?: 8,
            'mail.mailers.smtp.local_domain' => $localDomain,
        ]);

        if ($purgeResolvedMailers) {
            Mail::forgetMailers();
        }
    }
}
