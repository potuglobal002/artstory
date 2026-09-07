<?php

namespace App\Filament\Resources\ArtworkSubjectStyles\Pages;

use App\Filament\Resources\ArtworkSubjectStyles\ArtworkSubjectStyleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtworkSubjectStyles extends ListRecords
{
    protected static string $resource = ArtworkSubjectStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
