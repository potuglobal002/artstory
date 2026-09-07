<?php

namespace App\Filament\Resources\ArtworkSizes;

use App\Filament\Resources\ArtworkSizes\Pages\CreateArtworkSize;
use App\Filament\Resources\ArtworkSizes\Pages\EditArtworkSize;
use App\Filament\Resources\ArtworkSizes\Pages\ListArtworkSizes;
use App\Filament\Resources\ArtworkSizes\Pages\ViewArtworkSize;
use App\Filament\Resources\ArtworkSizes\Schemas\ArtworkSizeForm;
use App\Filament\Resources\ArtworkSizes\Schemas\ArtworkSizeInfolist;
use App\Filament\Resources\ArtworkSizes\Tables\ArtworkSizesTable;
use App\Models\ArtworkSize;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ArtworkSizeResource extends Resource
{
    protected static ?string $model = ArtworkSize::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsPointingOut;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Setup';

    protected static ?string $navigationLabel = 'Sizes';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return ArtworkSizeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtworkSizeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtworkSizesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworkSizes::route('/'),
            'create' => CreateArtworkSize::route('/create'),
            'view' => ViewArtworkSize::route('/{record}'),
            'edit' => EditArtworkSize::route('/{record}/edit'),
        ];
    }
}
