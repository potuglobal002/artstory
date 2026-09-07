<?php

namespace App\Filament\Resources\ArtworkPaymentMethods\Pages;

use App\Filament\Resources\ArtworkPaymentMethods\ArtworkPaymentMethodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtworkPaymentMethods extends ListRecords
{
    protected static string $resource = ArtworkPaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
