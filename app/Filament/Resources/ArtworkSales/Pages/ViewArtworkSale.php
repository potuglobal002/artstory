<?php

namespace App\Filament\Resources\ArtworkSales\Pages;

use App\Filament\Resources\ArtworkSales\ArtworkSaleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArtworkSale extends ViewRecord
{
    protected static string $resource = ArtworkSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ArtworkSaleResource::downloadInvoiceAction(),
            ArtworkSaleResource::sendInvoiceEmailAction(),
            EditAction::make(),
        ];
    }
}
