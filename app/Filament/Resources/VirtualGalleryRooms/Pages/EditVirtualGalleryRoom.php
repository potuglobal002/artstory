<?php

namespace App\Filament\Resources\VirtualGalleryRooms\Pages;

use App\Filament\Resources\VirtualGalleryRooms\VirtualGalleryRoomResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVirtualGalleryRoom extends EditRecord
{
    protected static string $resource = VirtualGalleryRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
