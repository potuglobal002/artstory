<?php

namespace App\Filament\Resources\ArtworkSales\Widgets;

use App\Models\ArtworkSale;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ArtworkSaleStatsOverview extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalSales = ArtworkSale::query()->count();
        $bdtRevenue = ArtworkSale::query()->where('currency', 'BDT')->sum('sold_price');
        $usdRevenue = ArtworkSale::query()->where('currency', 'USD')->sum('sold_price');
        $bdtAverage = ArtworkSale::query()->where('currency', 'BDT')->avg('sold_price') ?: 0;
        $usdAverage = ArtworkSale::query()->where('currency', 'USD')->avg('sold_price') ?: 0;

        return [
            Stat::make('Total Sales', number_format($totalSales))
                ->color('primary'),
            Stat::make('Total Revenue', 'BDT ' . number_format((float) $bdtRevenue, 0) . ' / USD ' . number_format((float) $usdRevenue, 2))
                ->color('success'),
            Stat::make('Avg Sale Value', 'BDT ' . number_format((float) $bdtAverage, 0) . ' / USD ' . number_format((float) $usdAverage, 2))
                ->color('info'),
        ];
    }
}
