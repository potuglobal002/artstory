<?php

namespace App\Filament\Resources\ArtworkSales\Pages;

use App\Filament\Resources\ArtworkSales\ArtworkSaleResource;
use App\Filament\Resources\ArtworkSales\Widgets\ArtworkSaleStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtworkSales extends ListRecords
{
    protected static string $resource = ArtworkSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ArtworkSaleStatsOverview::class,
        ];
    }
}
