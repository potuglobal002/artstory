<?php

namespace App\Filament\Resources\ArtworkPaymentMethods;

use App\Filament\Resources\ArtworkPaymentMethods\Pages\CreateArtworkPaymentMethod;
use App\Filament\Resources\ArtworkPaymentMethods\Pages\EditArtworkPaymentMethod;
use App\Filament\Resources\ArtworkPaymentMethods\Pages\ListArtworkPaymentMethods;
use App\Filament\Resources\ArtworkPaymentMethods\Pages\ViewArtworkPaymentMethod;
use App\Filament\Resources\ArtworkPaymentMethods\Schemas\ArtworkPaymentMethodForm;
use App\Filament\Resources\ArtworkPaymentMethods\Schemas\ArtworkPaymentMethodInfolist;
use App\Filament\Resources\ArtworkPaymentMethods\Tables\ArtworkPaymentMethodsTable;
use App\Models\ArtworkPaymentMethod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ArtworkPaymentMethodResource extends Resource
{
    protected static ?string $model = ArtworkPaymentMethod::class;

    protected static ?string $slug = 'artwork-payment-methods';

    protected static ?string $modelLabel = 'Payment Method';

    protected static ?string $pluralModelLabel = 'Payment Methods';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Setup';

    protected static ?string $navigationLabel = 'Payment Methods';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return ArtworkPaymentMethodForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtworkPaymentMethodInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtworkPaymentMethodsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworkPaymentMethods::route('/'),
            'create' => CreateArtworkPaymentMethod::route('/create'),
            'view' => ViewArtworkPaymentMethod::route('/{record}'),
            'edit' => EditArtworkPaymentMethod::route('/{record}/edit'),
        ];
    }
}
