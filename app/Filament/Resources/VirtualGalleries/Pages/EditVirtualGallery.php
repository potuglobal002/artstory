<?php

namespace App\Filament\Resources\VirtualGalleries\Pages;

use App\Filament\Resources\VirtualGalleries\VirtualGalleryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVirtualGallery extends EditRecord
{
    protected static string $resource = VirtualGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
