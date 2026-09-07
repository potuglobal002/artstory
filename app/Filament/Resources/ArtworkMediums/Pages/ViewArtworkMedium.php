<?php

namespace App\Filament\Resources\ArtworkMediums\Pages;

use App\Filament\Resources\ArtworkMediums\ArtworkMediumResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArtworkMedium extends ViewRecord
{
    protected static string $resource = ArtworkMediumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
