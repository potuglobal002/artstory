<?php

namespace App\Filament\Pages;

use App\Models\Artist;
use App\Models\Artwork;
use App\Support\ArtworkInventoryReportExporter;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

class ArtworkInventoryReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Catalogue';

    protected static ?string $navigationLabel = 'Inventory Report';

    protected static ?string $title = 'Inventory Report';

    protected static ?int $navigationSort = 25;

    protected string $view = 'filament.pages.artwork-inventory-report';

    public ?string $artistId = null;

    public ?string $year = null;

    public string $status = 'all';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:ArtworkInventoryReport') ?? false;
    }

    public function resetFilters(): void
    {
        $this->artistId = null;
        $this->year = null;
        $this->status = 'all';
    }

    public function setStatus(string $status): void
    {
        if (! in_array($status, ['all', 'available', 'sold'], true)) {
            return;
        }

        $this->status = $status;
    }

    public function downloadPdf(): BinaryFileResponse
    {
        return ArtworkInventoryReportExporter::downloadPdf(
            $this->exportArtworkRows(),
            $this->getSummary(),
            $this->getAppliedFilterLabels(),
        );
    }

    public function downloadXlsx(): BinaryFileResponse
    {
        return ArtworkInventoryReportExporter::downloadXlsx(
            $this->exportArtworkRows(),
            $this->getSummary(),
            $this->getAppliedFilterLabels(),
        );
    }

    public function getFilterOptions(): array
    {
        return [
            'artists' => Artist::query()->orderBy('name')->pluck('name', 'id'),
            'years' => Artwork::query()->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year', 'year'),
        ];
    }

    public function getSummary(): array
    {
        $rows = $this->baseQuery()->get();

        return [
            'total' => $rows->count(),
            'available' => $rows->where('status', 'available')->count(),
            'sold' => $rows->where('status', 'sold')->count(),
        ];
    }

    public function getStatusCounts(): array
    {
        $rows = $this->baseQuery(false)->get();

        return [
            'all' => $rows->count(),
            'available' => $rows->where('status', 'available')->count(),
            'sold' => $rows->where('status', 'sold')->count(),
        ];
    }

    public function getArtworkRows(): Collection
    {
        return $this->baseQuery()
            ->latest()
            ->limit(200)
            ->get();
    }

    public function getAppliedFilterLabels(): array
    {
        $options = $this->getFilterOptions();

        return [
            'artist' => $this->artistId ? (string) ($options['artists'][$this->artistId] ?? 'Selected artist') : 'All artists',
            'year' => $this->year ?: 'All years',
            'status' => $this->status === 'all' ? 'Available + Sold' : ucfirst($this->status),
        ];
    }

    private function exportArtworkRows(): Collection
    {
        return $this->baseQuery()
            ->latest()
            ->get();
    }

    private function baseQuery(bool $withStatus = true): Builder
    {
        return Artwork::query()
            ->with(['artist', 'style', 'subjectStyle', 'medium', 'size'])
            ->when($this->artistId, fn (Builder $query) => $query->where('artist_id', $this->artistId))
            ->when($this->year, fn (Builder $query) => $query->where('year', $this->year))
            ->when($withStatus && $this->status !== 'all', fn (Builder $query) => $query->where('status', $this->status));
    }
}
