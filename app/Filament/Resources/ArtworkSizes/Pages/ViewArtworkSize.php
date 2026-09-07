<?php

namespace App\Filament\Resources\ArtworkSizes\Pages;

use App\Filament\Resources\ArtworkSizes\ArtworkSizeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArtworkSize extends ViewRecord
{
    protected static string $resource = ArtworkSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
