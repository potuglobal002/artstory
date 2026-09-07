<?php

namespace App\Filament\Widgets;

use App\Models\Artwork;
use App\Models\ArtworkSale;
use App\Models\Artist;
use App\Models\Buyer;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class ArtworkDashboardPanel extends Widget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.artwork-dashboard-panel';

    protected ?string $pollingInterval = null;

    public function getAvailableCount(): int
    {
        return Artwork::query()->where('status', 'available')->count();
    }

    public function getSoldCount(): int
    {
        return Artwork::query()->where('status', 'sold')->count();
    }

    public function getTotalCount(): int
    {
        return Artwork::query()->count();
    }

    public function getTotalRevenue(string $currency = 'BDT'): float
    {
        return (float) ArtworkSale::query()->where('currency', strtoupper($currency))->sum('sold_price');
    }

    public function getTotalSales(): int
    {
        return ArtworkSale::query()->count();
    }

    public function getArtistCount(): int
    {
        return Artist::query()->count();
    }

    public function getBuyerCount(): int
    {
        return Buyer::query()->count();
    }

    /**
     * @return Collection<int, ArtworkSale>
     */
    public function getRecentSales(): Collection
    {
        return ArtworkSale::query()
            ->with(['artwork', 'artist', 'buyer'])
            ->latest('sold_at')
            ->latest('id')
            ->limit(6)
            ->get();
    }

    /**
     * @return Collection<int, object>
     */
    public function getTopArtists(): Collection
    {
        return Artwork::query()
            ->selectRaw('artist_id, COUNT(*) as artworks_count')
            ->with('artist:id,name')
            ->whereNotNull('artist_id')
            ->groupBy('artist_id')
            ->orderByDesc('artworks_count')
            ->limit(5)
            ->get();
    }

    public function money(mixed $value, string $currency = 'BDT'): string
    {
        $currency = strtoupper($currency ?: 'BDT');

        return $currency . ' ' . number_format((float) $value, $currency === 'BDT' ? 0 : 2);
    }
}
