<?php

namespace App\Filament\Resources\ArtworkTaxRates\Pages;

use App\Filament\Resources\ArtworkTaxRates\ArtworkTaxRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtworkTaxRates extends ListRecords
{
    protected static string $resource = ArtworkTaxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
