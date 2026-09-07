<?php

namespace App\Filament\Resources\Buyers;

use App\Filament\Resources\Buyers\Pages\CreateBuyer;
use App\Filament\Resources\Buyers\Pages\EditBuyer;
use App\Filament\Resources\Buyers\Pages\ListBuyers;
use App\Filament\Resources\Buyers\Pages\ViewBuyer;
use App\Filament\Resources\Buyers\Schemas\BuyerForm;
use App\Filament\Resources\Buyers\Schemas\BuyerInfolist;
use App\Filament\Resources\Buyers\Tables\BuyersTable;
use App\Models\Buyer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BuyerResource extends Resource
{
    protected static ?string $model = Buyer::class;

    protected static ?string $slug = 'buyers';

    protected static ?string $modelLabel = 'Buyer';

    protected static ?string $pluralModelLabel = 'Buyers';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork People';

    protected static ?string $navigationLabel = 'Buyers';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 24;

    public static function form(Schema $schema): Schema
    {
        return BuyerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BuyerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BuyersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBuyers::route('/'),
            'create' => CreateBuyer::route('/create'),
            'view' => ViewBuyer::route('/{record}'),
            'edit' => EditBuyer::route('/{record}/edit'),
        ];
    }
}
