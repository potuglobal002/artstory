<?php

namespace App\Filament\Resources\ArtworkSubjectStyles;

use App\Filament\Resources\ArtworkSubjectStyles\Pages\CreateArtworkSubjectStyle;
use App\Filament\Resources\ArtworkSubjectStyles\Pages\EditArtworkSubjectStyle;
use App\Filament\Resources\ArtworkSubjectStyles\Pages\ListArtworkSubjectStyles;
use App\Filament\Resources\ArtworkSubjectStyles\Pages\ViewArtworkSubjectStyle;
use App\Filament\Resources\ArtworkSubjectStyles\Schemas\ArtworkSubjectStyleForm;
use App\Filament\Resources\ArtworkSubjectStyles\Schemas\ArtworkSubjectStyleInfolist;
use App\Filament\Resources\ArtworkSubjectStyles\Tables\ArtworkSubjectStylesTable;
use App\Models\ArtworkSubjectStyle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ArtworkSubjectStyleResource extends Resource
{
    protected static ?string $model = ArtworkSubjectStyle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Setup';

    protected static ?string $navigationLabel = 'Subject Styles';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 35;

    public static function form(Schema $schema): Schema
    {
        return ArtworkSubjectStyleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtworkSubjectStyleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtworkSubjectStylesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworkSubjectStyles::route('/'),
            'create' => CreateArtworkSubjectStyle::route('/create'),
            'view' => ViewArtworkSubjectStyle::route('/{record}'),
            'edit' => EditArtworkSubjectStyle::route('/{record}/edit'),
        ];
    }
}
