<?php

namespace App\Filament\Resources\ArtworkTaxRates\Pages;

use App\Filament\Resources\ArtworkTaxRates\ArtworkTaxRateResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArtworkTaxRate extends ViewRecord
{
    protected static string $resource = ArtworkTaxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
