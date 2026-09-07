<?php

namespace App\Filament\Resources\ArtworkSales\Schemas;

use App\Models\Artwork;
use App\Models\ArtworkSale;
use App\Models\ArtworkTaxRate;
use App\Models\Buyer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ArtworkSaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Sale Details')
                    ->schema([
                        DatePicker::make('sold_at')
                            ->label('Sale Date')
                            ->default(now())
                            ->native(false)
                            ->required()
                            ->columnSpan(1),
                        Select::make('currency')
                            ->label('Sale Currency')
                            ->options([
                                'BDT' => 'BDT - Bangladesh',
                                'USD' => 'USD - Other countries',
                            ])
                            ->default('BDT')
                            ->live()
                            ->required()
                            ->columnSpan(1),
                        Select::make('payment_method_id')
                            ->label('Paid By')
                            ->relationship('paymentMethod', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->preload()
                            ->searchable()
                            ->createOptionForm(self::paymentMethodCreateForm())
                            ->createOptionModalHeading('Add Payment Method')
                            ->live()
                            ->columnSpan(1),
                        Select::make('tax_rate_id')
                            ->label('Tax Rule')
                            ->options(fn (Get $get): array => ArtworkTaxRate::query()
                                ->with('paymentMethod')
                                ->where('is_active', true)
                                ->where('currency', $get('currency') ?: 'BDT')
                                ->orderBy('country_name')
                                ->orderBy('payment_method_id')
                                ->orderByDesc('version')
                                ->get()
                                ->mapWithKeys(fn (ArtworkTaxRate $rate): array => [$rate->id => $rate->label()])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                $rate = $state ? ArtworkTaxRate::query()->find($state) : null;

                                if (! $rate) {
                                    return;
                                }

                                foreach ($rate->snapshot() as $field => $value) {
                                    $set($field, $value);
                                }
                            })
                            ->helperText('Choose the current tax version; the invoice stores this snapshot.')
                            ->columnSpan(2),
                        TextInput::make('tax_country_name')
                            ->label('Tax Country')
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('tax_percentage')
                            ->label('Tax %')
                            ->numeric()
                            ->minValue(0)
                            ->step('0.001')
                            ->default(0)
                            ->suffix('%')
                            ->helperText('Editable before saving. BDT cash can stay 0%; other BDT payments often use 15%; USD default is 8.875%.')
                            ->columnSpan(1),
                        Toggle::make('is_tax_included')
                            ->label('Tax included in item prices')
                            ->default(false)
                            ->inline(false)
                            ->columnSpan(1),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 4,
                    ])
                    ->columnSpanFull(),
                Section::make('Purchased Artworks')
                    ->description('Add every artwork bought by this buyer. One sale creates one invoice and one email confirmation.')
                    ->schema([
                        Repeater::make('line_items')
                            ->label('Artwork items')
                            ->hiddenLabel()
                            ->schema([
                                Select::make('artwork_id')
                                    ->label('Artwork')
                                    ->options(fn (Get $get): array => self::availableArtworkOptions($get('artwork_id')))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('sold_price')
                                    ->label('Sold Price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix(fn (Get $get): string => $get('../../currency') === 'USD' ? '$' : '৳')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->reorderable(false)
                            ->collapsible()
                            ->addActionLabel('Add another artwork')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Buyer Information')
                    ->schema([
                        Select::make('buyer_id')
                            ->label('Buyer')
                            ->relationship('buyer', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->getOptionLabelFromRecordUsing(fn (Buyer $record): string => $record->label())
                            ->preload()
                            ->searchable()
                            ->createOptionForm(self::buyerCreateForm())
                            ->createOptionModalHeading('Add Buyer'),
                        TextInput::make('buyer_name')
                            ->label('Buyer Name')
                            ->maxLength(255),
                        TextInput::make('buyer_designation')
                            ->label('Designation')
                            ->maxLength(255),
                        TextInput::make('buyer_phone')
                            ->label('Buyer Phone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('buyer_email')
                            ->label('Buyer Email')
                            ->email()
                            ->maxLength(255),
                        Textarea::make('note')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    private static function buyerCreateForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Buyer Name')
                ->required()
                ->maxLength(255),
            TextInput::make('designation')
                ->maxLength(255),
            TextInput::make('phone')
                ->tel()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->maxLength(255),
            Textarea::make('address')
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('note')
                ->rows(3)
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label('Active')
                ->default(true)
                ->inline(false),
        ];
    }

    private static function availableArtworkOptions(mixed $currentArtworkId = null): array
    {
        $soldArtworkIds = self::soldArtworkIds();
        $currentArtworkId = $currentArtworkId ? (int) $currentArtworkId : null;

        return Artwork::query()
            ->with('artist')
            ->where('is_active', true)
            ->where(function ($query) use ($soldArtworkIds, $currentArtworkId): void {
                $query->where('status', 'available');

                if ($currentArtworkId) {
                    $query->orWhereKey($currentArtworkId);
                }
            })
            ->when(
                $soldArtworkIds !== [],
                fn ($query) => $query->whereNotIn('id', $currentArtworkId ? array_diff($soldArtworkIds, [$currentArtworkId]) : $soldArtworkIds)
            )
            ->orderBy('title')
            ->get()
            ->mapWithKeys(fn (Artwork $artwork): array => [
                $artwork->id => $artwork->title . ' - ' . ($artwork->artist?->name ?: 'Unknown artist'),
            ])
            ->all();
    }

    private static function soldArtworkIds(): array
    {
        return ArtworkSale::query()
            ->where('is_active', true)
            ->get()
            ->flatMap(fn (ArtworkSale $sale) => $sale->displayLineItems()->pluck('artwork_id'))
            ->filter()
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private static function paymentMethodCreateForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Payment Method')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Textarea::make('description')
                ->rows(3)
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label('Active')
                ->default(true)
                ->inline(false),
        ];
    }
}
