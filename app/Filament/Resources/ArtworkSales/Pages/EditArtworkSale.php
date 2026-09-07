<?php

namespace App\Filament\Resources\ArtworkSales\Pages;

use App\Filament\Resources\ArtworkSales\ArtworkSaleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArtworkSale extends EditRecord
{
    protected static string $resource = ArtworkSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ArtworkSaleResource::downloadInvoiceAction(),
            ArtworkSaleResource::sendInvoiceEmailAction(),
            DeleteAction::make(),
        ];
    }
}
