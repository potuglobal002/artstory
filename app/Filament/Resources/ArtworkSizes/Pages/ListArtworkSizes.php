<?php

namespace App\Filament\Resources\ArtworkSizes\Pages;

use App\Filament\Resources\ArtworkSizes\ArtworkSizeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtworkSizes extends ListRecords
{
    protected static string $resource = ArtworkSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
