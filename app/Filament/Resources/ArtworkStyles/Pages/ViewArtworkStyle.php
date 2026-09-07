<?php

namespace App\Filament\Resources\ArtworkStyles\Pages;

use App\Filament\Resources\ArtworkStyles\ArtworkStyleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArtworkStyle extends ViewRecord
{
    protected static string $resource = ArtworkStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
