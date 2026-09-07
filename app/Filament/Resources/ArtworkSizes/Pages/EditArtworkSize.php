<?php

namespace App\Filament\Resources\ArtworkSizes\Pages;

use App\Filament\Resources\ArtworkSizes\ArtworkSizeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditArtworkSize extends EditRecord
{
    protected static string $resource = ArtworkSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
