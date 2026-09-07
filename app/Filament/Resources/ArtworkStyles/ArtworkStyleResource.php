<?php

namespace App\Filament\Resources\ArtworkStyles;

use App\Filament\Resources\ArtworkStyles\Pages\CreateArtworkStyle;
use App\Filament\Resources\ArtworkStyles\Pages\EditArtworkStyle;
use App\Filament\Resources\ArtworkStyles\Pages\ListArtworkStyles;
use App\Filament\Resources\ArtworkStyles\Pages\ViewArtworkStyle;
use App\Filament\Resources\ArtworkStyles\Schemas\ArtworkStyleForm;
use App\Filament\Resources\ArtworkStyles\Schemas\ArtworkStyleInfolist;
use App\Filament\Resources\ArtworkStyles\Tables\ArtworkStylesTable;
use App\Models\ArtworkStyle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ArtworkStyleResource extends Resource
{
    protected static ?string $model = ArtworkStyle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Setup';

    protected static ?string $navigationLabel = 'Styles';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return ArtworkStyleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtworkStyleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtworkStylesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworkStyles::route('/'),
            'create' => CreateArtworkStyle::route('/create'),
            'view' => ViewArtworkStyle::route('/{record}'),
            'edit' => EditArtworkStyle::route('/{record}/edit'),
        ];
    }
}
