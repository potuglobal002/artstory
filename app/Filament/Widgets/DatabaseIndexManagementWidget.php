<?php

namespace App\Filament\Widgets;

use App\Support\DatabaseIndexManager;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class DatabaseIndexManagementWidget extends Widget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.database-index-management-widget';

    public ?string $lastAction = null;

    public ?string $lastActionAt = null;

    public static function canView(): bool
    {
        return auth()->user()?->can('View:DatabaseIndexManagementWidget') ?? false;
    }

    public function getRows(): Collection
    {
        return app(DatabaseIndexManager::class)->rows();
    }

    public function getMissingCount(): int
    {
        return app(DatabaseIndexManager::class)->missingCount();
    }

    public function applyMissingIndexes(): void
    {
        $applied = app(DatabaseIndexManager::class)->applyMissing();

        $this->lastAction = $applied === 1
            ? '1 missing index applied'
            : $applied . ' missing indexes applied';
        $this->lastActionAt = now()->timezone(config('app.timezone'))->format('M d, Y h:i A');

        Notification::make()
            ->success()
            ->title('Database indexes updated')
            ->body($applied > 0 ? $this->lastAction : 'All recommended indexes are already installed.')
            ->send();
    }

    public function refreshIndexStatus(): void
    {
        $this->lastAction = 'Index status refreshed';
        $this->lastActionAt = now()->timezone(config('app.timezone'))->format('M d, Y h:i A');

        Notification::make()
            ->success()
            ->title('Index status refreshed')
            ->send();
    }
}
