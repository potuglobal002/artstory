<?php

namespace App\Filament\Resources\ArtworkTaxRates;

use App\Filament\Resources\ArtworkTaxRates\Pages\CreateArtworkTaxRate;
use App\Filament\Resources\ArtworkTaxRates\Pages\EditArtworkTaxRate;
use App\Filament\Resources\ArtworkTaxRates\Pages\ListArtworkTaxRates;
use App\Filament\Resources\ArtworkTaxRates\Pages\ViewArtworkTaxRate;
use App\Models\ArtworkTaxRate;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class ArtworkTaxRateResource extends Resource
{
    protected static ?string $model = ArtworkTaxRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Setup';

    protected static ?string $navigationLabel = 'Tax Rates';

    protected static ?string $modelLabel = 'Tax Rate';

    protected static ?string $pluralModelLabel = 'Tax Rates';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 24;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tax Rule')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tax Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('country_code')
                            ->label('Country Code')
                            ->maxLength(8)
                            ->placeholder('BD')
                            ->helperText('Leave blank for international/default USD tax.'),
                        TextInput::make('country_name')
                            ->label('Country Name')
                            ->required()
                            ->default('Bangladesh')
                            ->maxLength(255),
                        Select::make('payment_method_id')
                            ->label('Payment Method')
                            ->relationship('paymentMethod', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->preload()
                            ->searchable()
                            ->helperText('Leave blank when this tax applies to any payment method.'),
                        Select::make('currency')
                            ->options([
                                'BDT' => 'BDT',
                                'USD' => 'USD',
                            ])
                            ->default('BDT')
                            ->required(),
                        TextInput::make('percentage')
                            ->label('Tax Percentage')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step('0.001')
                            ->suffix('%'),
                        TextInput::make('version')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                        Toggle::make('is_tax_included')
                            ->label('Tax already included in item price')
                            ->default(false)
                            ->inline(false),
                        DatePicker::make('effective_from')
                            ->native(false),
                        DatePicker::make('effective_until')
                            ->native(false),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),
                        Textarea::make('note')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tax Rule')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('country_name')->label('Country'),
                        TextEntry::make('country_code')->label('Country Code')->placeholder('-'),
                        TextEntry::make('paymentMethod.name')->label('Payment Method')->placeholder('Any payment'),
                        TextEntry::make('currency'),
                        TextEntry::make('percentage')->suffix('%'),
                        TextEntry::make('version'),
                        IconEntry::make('is_tax_included')->label('Tax Included')->boolean(),
                        TextEntry::make('effective_from')->date('M d, Y')->placeholder('-'),
                        TextEntry::make('effective_until')->date('M d, Y')->placeholder('-'),
                        IconEntry::make('is_active')->label('Active')->boolean(),
                        TextEntry::make('note')->placeholder('-')->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('country_name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('country_name')->label('Country')->searchable()->sortable(),
                TextColumn::make('country_code')->label('Code')->placeholder('-'),
                TextColumn::make('paymentMethod.name')->label('Payment')->placeholder('Any payment'),
                TextColumn::make('currency')->badge(),
                TextColumn::make('percentage')->suffix('%')->sortable(),
                TextColumn::make('version')->badge()->sortable(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('currency')
                    ->options([
                        'BDT' => 'BDT',
                        'USD' => 'USD',
                    ]),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworkTaxRates::route('/'),
            'create' => CreateArtworkTaxRate::route('/create'),
            'view' => ViewArtworkTaxRate::route('/{record}'),
            'edit' => EditArtworkTaxRate::route('/{record}/edit'),
        ];
    }
}
