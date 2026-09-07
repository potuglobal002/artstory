<?php

namespace App\Filament\Widgets;

use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Throwable;

class CacheManagementWidget extends Widget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.cache-management-widget';

    public ?string $lastAction = null;

    public ?string $lastActionAt = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('View:CacheManagementWidget') ?? false;
    }

    public function clearAll(): void
    {
        $this->runCommands('All cache cleared', [
            'optimize:clear',
            'filament:optimize-clear',
        ]);
    }

    public function rebuildOptimizedCache(): void
    {
        $this->runCommands('Optimized cache rebuilt', [
            'optimize',
            'filament:optimize',
        ]);
    }

    public function clearApplicationCache(): void
    {
        $this->runCommands('Application cache cleared', ['cache:clear']);
    }

    public function clearConfigCache(): void
    {
        $this->runCommands('Config cache cleared', ['config:clear']);
    }

    public function clearRouteCache(): void
    {
        $this->runCommands('Route cache cleared', ['route:clear']);
    }

    public function clearViewCache(): void
    {
        $this->runCommands('View cache cleared', ['view:clear']);
    }

    public function clearEventCache(): void
    {
        $this->runCommands('Event cache cleared', ['event:clear']);
    }

    public function clearFilamentCache(): void
    {
        $this->runCommands('Filament cache cleared', ['filament:optimize-clear']);
    }

    /**
     * @return array<int, array{label: string, value: string, active: bool}>
     */
    public function getCacheStatuses(): array
    {
        return Cache::remember('admin.dashboard.cache_statuses', now()->addMinute(), fn (): array => [
            [
                'label' => 'Config',
                'value' => app()->configurationIsCached() ? 'Cached' : 'Not cached',
                'active' => app()->configurationIsCached(),
            ],
            [
                'label' => 'Routes',
                'value' => app()->routesAreCached() ? 'Cached' : 'Not cached',
                'active' => app()->routesAreCached(),
            ],
            [
                'label' => 'Views',
                'value' => number_format($this->countFiles(storage_path('framework/views'))) . ' files',
                'active' => $this->countFiles(storage_path('framework/views')) > 0,
            ],
            [
                'label' => 'App cache',
                'value' => number_format($this->countFiles(storage_path('framework/cache/data'))) . ' files',
                'active' => $this->countFiles(storage_path('framework/cache/data')) > 0,
            ],
            [
                'label' => 'Filament',
                'value' => File::exists($this->bootstrapCachePath('filament')) ? 'Cached' : 'Ready',
                'active' => File::exists($this->bootstrapCachePath('filament')),
            ],
        ]);
    }

    /**
     * @param  array<int, string>  $commands
     */
    private function runCommands(string $message, array $commands): void
    {
        $skippedCommands = [];

        foreach ($commands as $command) {
            if (! array_key_exists($command, Artisan::all())) {
                $skippedCommands[] = $command;

                continue;
            }

            try {
                Artisan::call($command);
            } catch (Throwable) {
                $skippedCommands[] = $command;
            }
        }

        Cache::forget('admin.dashboard.cache_statuses');
        Cache::forget('admin.dashboard.enrollment_stats');
        Cache::forget('admin.dashboard.payment_gateway_breakdown');
        Cache::forget('admin.dashboard.database_indexes');

        $this->lastAction = $message;
        $this->lastActionAt = now()->timezone(config('app.timezone'))->format('M d, Y h:i A');

        $notification = Notification::make()
            ->title($message)
            ->body($skippedCommands === []
                ? 'Dashboard cache status has been refreshed.'
                : 'Cache refreshed. Optional commands skipped: ' . implode(', ', $skippedCommands));

        $skippedCommands === []
            ? $notification->success()->send()
            : $notification->warning()->send();
    }

    private function countFiles(string $path): int
    {
        if (! File::isDirectory($path)) {
            return 0;
        }

        return collect(File::allFiles($path))->count();
    }

    private function bootstrapCachePath(string $path = ''): string
    {
        return base_path('bootstrap/cache' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }
}
