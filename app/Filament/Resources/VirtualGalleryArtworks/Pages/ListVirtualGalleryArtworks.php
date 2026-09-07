<?php

namespace App\Filament\Resources\VirtualGalleryArtworks\Pages;

use App\Filament\Resources\VirtualGalleryArtworks\VirtualGalleryArtworkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVirtualGalleryArtworks extends ListRecords
{
    protected static string $resource = VirtualGalleryArtworkResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
