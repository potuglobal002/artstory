<?php

namespace App\Filament\Resources\ArtworkMediums;

use App\Filament\Resources\ArtworkMediums\Pages\CreateArtworkMedium;
use App\Filament\Resources\ArtworkMediums\Pages\EditArtworkMedium;
use App\Filament\Resources\ArtworkMediums\Pages\ListArtworkMediums;
use App\Filament\Resources\ArtworkMediums\Pages\ViewArtworkMedium;
use App\Filament\Resources\ArtworkMediums\Schemas\ArtworkMediumForm;
use App\Filament\Resources\ArtworkMediums\Schemas\ArtworkMediumInfolist;
use App\Filament\Resources\ArtworkMediums\Tables\ArtworkMediumsTable;
use App\Models\ArtworkMedium;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ArtworkMediumResource extends Resource
{
    protected static ?string $model = ArtworkMedium::class;

    protected static ?string $slug = 'artwork-mediums';

    protected static ?string $modelLabel = 'Medium';

    protected static ?string $pluralModelLabel = 'Mediums';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Setup';

    protected static ?string $navigationLabel = 'Mediums';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 36;

    public static function form(Schema $schema): Schema
    {
        return ArtworkMediumForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtworkMediumInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtworkMediumsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworkMediums::route('/'),
            'create' => CreateArtworkMedium::route('/create'),
            'view' => ViewArtworkMedium::route('/{record}'),
            'edit' => EditArtworkMedium::route('/{record}/edit'),
        ];
    }
}
