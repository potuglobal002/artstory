<?php

namespace App\Http\Middleware;

use App\Support\SiteSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendFeatureEnabled
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $settings = SiteSettings::current();
        $enabled = match ($feature) {
            'virtual_gallery' => $settings?->virtual_gallery_enabled ?? true,
            'artist_login' => $settings?->artist_login_enabled ?? true,
            'exhibitions' => $settings?->exhibition_enabled ?? true,
            'events_pr' => $settings?->events_pr_enabled ?? true,
            default => false,
        };

        abort_unless($enabled, 404);

        return $next($request);
    }
}
