<?php

namespace App\Filament\Resources\ArtworkMediums\Pages;

use App\Filament\Resources\ArtworkMediums\ArtworkMediumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtworkMediums extends ListRecords
{
    protected static string $resource = ArtworkMediumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
