<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SiteSettings
{
    private static ?SiteSetting $current = null;

    public static function current(): ?SiteSetting
    {
        if (static::$current) {
            return static::$current;
        }

        if (! Schema::hasTable('site_settings')) {
            return null;
        }

        try {
            $attributes = Cache::remember('settings.site', now()->addMinutes(30), fn (): array => SiteSetting::current()->getAttributes());

            // Older cached settings used attributesToArray(), which turns JSON
            // columns into PHP arrays. setRawAttributes() requires database JSON.
            foreach (['footer_section_links', 'footer_collector_links'] as $attribute) {
                if (is_array($attributes[$attribute] ?? null)) {
                    $attributes[$attribute] = json_encode($attributes[$attribute]);
                }
            }

            return static::$current = (new SiteSetting)->setRawAttributes($attributes, true);
        } catch (Throwable) {
            return null;
        }
    }

    public static function title(): string
    {
        return static::current()?->site_title ?: config('app.name', 'ART Story');
    }

    public static function forget(): void
    {
        static::$current = null;
        Cache::forget('settings.site');
    }

    public static function adminLogoUrl(): ?string
    {
        return static::current()?->adminLogoUrl();
    }

    public static function faviconUrl(): ?string
    {
        return static::current()?->faviconUrl();
    }
}
