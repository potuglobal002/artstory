<?php

namespace App\Filament\Resources\ArtworkStyles\Pages;

use App\Filament\Resources\ArtworkStyles\ArtworkStyleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditArtworkStyle extends EditRecord
{
    protected static string $resource = ArtworkStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
