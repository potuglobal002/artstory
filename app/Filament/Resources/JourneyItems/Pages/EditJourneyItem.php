<?php

namespace App\Filament\Resources\JourneyItems\Pages;

use App\Filament\Resources\JourneyItems\JourneyItemResource;
use Filament\Resources\Pages\EditRecord;

class EditJourneyItem extends EditRecord
{
    protected static string $resource = JourneyItemResource::class;
}
