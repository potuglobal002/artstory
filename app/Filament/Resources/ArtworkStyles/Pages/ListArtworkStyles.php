<?php

namespace App\Filament\Resources\ArtworkStyles\Pages;

use App\Filament\Resources\ArtworkStyles\ArtworkStyleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtworkStyles extends ListRecords
{
    protected static string $resource = ArtworkStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
