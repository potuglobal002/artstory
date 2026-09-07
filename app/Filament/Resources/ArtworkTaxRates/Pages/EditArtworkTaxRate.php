<?php

namespace App\Filament\Resources\ArtworkTaxRates\Pages;

use App\Filament\Resources\ArtworkTaxRates\ArtworkTaxRateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditArtworkTaxRate extends EditRecord
{
    protected static string $resource = ArtworkTaxRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
