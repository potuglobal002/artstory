<?php

namespace App\Filament\Resources\Artworks;

use App\Filament\Resources\Artworks\Pages\CreateArtwork;
use App\Filament\Resources\Artworks\Pages\EditArtwork;
use App\Filament\Resources\Artworks\Pages\ListArtworks;
use App\Filament\Resources\Artworks\Pages\ViewArtwork;
use App\Filament\Resources\Artworks\Schemas\ArtworkForm;
use App\Filament\Resources\Artworks\Schemas\ArtworkInfolist;
use App\Filament\Resources\Artworks\Tables\ArtworksTable;
use App\Models\Artwork;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ArtworkResource extends Resource
{
    protected static ?string $model = Artwork::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Catalogue';

    protected static ?string $navigationLabel = 'Artworks';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ArtworkForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtworkInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtworksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworks::route('/'),
            'create' => CreateArtwork::route('/create'),
            'view' => ViewArtwork::route('/{record}'),
            'edit' => EditArtwork::route('/{record}/edit'),
        ];
    }
}
