<?php

namespace App\Filament\Widgets;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkSale;
use App\Models\Buyer;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ArtworkDashboardOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalArtworks = Artwork::query()->count();
        $availableArtworks = Artwork::query()->where('status', 'available')->count();
        $soldArtworks = Artwork::query()->where('status', 'sold')->count();
        $totalSales = ArtworkSale::query()->count();
        $bdtRevenue = ArtworkSale::query()->where('currency', 'BDT')->sum('sold_price');
        $usdRevenue = ArtworkSale::query()->where('currency', 'USD')->sum('sold_price');
        $artistCount = Artist::query()->count();
        $buyerCount = Buyer::query()->count();

        return [
            Stat::make('Total Artworks', number_format($totalArtworks))
                ->description(number_format($availableArtworks) . ' available, ' . number_format($soldArtworks) . ' sold')
                ->descriptionIcon('heroicon-m-photo')
                ->color('primary'),
            Stat::make('Sales Revenue', 'BDT ' . number_format((float) $bdtRevenue, 0) . ' / USD ' . number_format((float) $usdRevenue, 2))
                ->description(number_format($totalSales) . ' confirmed sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Artists', number_format($artistCount))
                ->description(number_format($buyerCount) . ' customers recorded')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
