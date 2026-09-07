<?php

namespace App\Filament\Resources\JourneyItems\Pages;

use App\Filament\Resources\JourneyItems\JourneyItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJourneyItems extends ListRecords
{
    protected static string $resource = JourneyItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
