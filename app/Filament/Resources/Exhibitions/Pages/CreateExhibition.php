<?php

namespace App\Filament\Resources\Exhibitions\Pages;

use App\Filament\Resources\Exhibitions\ExhibitionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateExhibition extends CreateRecord
{
    protected static string $resource = ExhibitionResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}
