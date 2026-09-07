<?php

namespace App\Filament\Resources\VirtualGalleries\Pages;

use App\Filament\Resources\VirtualGalleries\VirtualGalleryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVirtualGalleries extends ListRecords
{
    protected static string $resource = VirtualGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
