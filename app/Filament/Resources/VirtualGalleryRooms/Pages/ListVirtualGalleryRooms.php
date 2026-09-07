<?php

namespace App\Filament\Resources\VirtualGalleryRooms\Pages;

use App\Filament\Resources\VirtualGalleryRooms\VirtualGalleryRoomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVirtualGalleryRooms extends ListRecords
{
    protected static string $resource = VirtualGalleryRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
