<?php

namespace App\Support;

use App\Models\LandingPage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class LandingPageSettings
{
    public static function current(): ?LandingPage
    {
        if (! Schema::hasTable('landing_pages')) {
            return null;
        }

        try {
            $attributes = Cache::remember('settings.landing_page', now()->addMinutes(30), fn (): array => LandingPage::current()->getAttributes());

            return (new LandingPage())->setRawAttributes($attributes, true);
        } catch (Throwable) {
            return null;
        }
    }

    public static function forget(): void
    {
        Cache::forget('settings.landing_page');
    }
}
