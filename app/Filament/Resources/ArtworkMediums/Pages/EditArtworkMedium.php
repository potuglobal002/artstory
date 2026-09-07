<?php

namespace App\Filament\Resources\ArtworkMediums\Pages;

use App\Filament\Resources\ArtworkMediums\ArtworkMediumResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditArtworkMedium extends EditRecord
{
    protected static string $resource = ArtworkMediumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
