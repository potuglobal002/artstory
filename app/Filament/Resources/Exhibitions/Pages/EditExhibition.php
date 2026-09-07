<?php

namespace App\Filament\Resources\Exhibitions\Pages;

use App\Filament\Resources\Exhibitions\ExhibitionResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditExhibition extends EditRecord
{
    protected static string $resource = ExhibitionResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}
