<?php

namespace App\Filament\Resources\ArtworkSubjectStyles\Pages;

use App\Filament\Resources\ArtworkSubjectStyles\ArtworkSubjectStyleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditArtworkSubjectStyle extends EditRecord
{
    protected static string $resource = ArtworkSubjectStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
