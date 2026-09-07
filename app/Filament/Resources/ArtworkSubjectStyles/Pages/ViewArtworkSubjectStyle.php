<?php

namespace App\Filament\Resources\ArtworkSubjectStyles\Pages;

use App\Filament\Resources\ArtworkSubjectStyles\ArtworkSubjectStyleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArtworkSubjectStyle extends ViewRecord
{
    protected static string $resource = ArtworkSubjectStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
