<?php

namespace App\Filament\Resources\VirtualGalleryArtworks\Pages;

use App\Filament\Resources\VirtualGalleryArtworks\VirtualGalleryArtworkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVirtualGalleryArtwork extends EditRecord
{
    protected static string $resource = VirtualGalleryArtworkResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
