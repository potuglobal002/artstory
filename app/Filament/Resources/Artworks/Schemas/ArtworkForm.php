<?php

namespace App\Filament\Resources\Artworks\Schemas;

use App\Models\ArtworkSize;
use App\Support\ArtworkImageOptimizer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ArtworkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Artwork Details')
                    ->schema([
                        Select::make('artist_id')
                            ->relationship('artist', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->label('Artist')
                            ->preload()
                            ->searchable()
                            ->createOptionForm(self::artistCreateForm())
                            ->createOptionModalHeading('Add Artist')
                            ->required(),
                        TextInput::make('artwork_code')
                            ->label('Artwork Code')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Generated automatically from artist initials, for example AGB-P-01.')
                            ->visibleOn('edit'),
                        TextInput::make('title')
                            ->label('Artwork Title')
                            ->maxLength(255),
                        Select::make('artwork_style_id')
                            ->relationship('style', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->label('Style')
                            ->preload()
                            ->searchable()
                            ->createOptionForm(self::simpleCreateForm('Style'))
                            ->createOptionModalHeading('Add Style'),
                        Select::make('artwork_subject_style_id')
                            ->relationship('subjectStyle', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->label('Subject Style')
                            ->preload()
                            ->searchable()
                            ->createOptionForm(self::simpleCreateForm('Subject Style'))
                            ->createOptionModalHeading('Add Subject Style'),
                        Select::make('artwork_medium_id')
                            ->relationship('medium', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->label('Medium')
                            ->preload()
                            ->searchable()
                            ->createOptionForm(self::simpleCreateForm('Medium'))
                            ->createOptionModalHeading('Add Medium'),
                        Select::make('artwork_size_id')
                            ->relationship('size', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->getOptionLabelFromRecordUsing(fn (ArtworkSize $record): string => $record->label())
                            ->label('Size')
                            ->preload()
                            ->searchable()
                            ->createOptionForm(self::sizeCreateForm())
                            ->createOptionModalHeading('Add Size'),
                        TextInput::make('year')
                            ->numeric()
                            ->minValue(1000)
                            ->maxValue((int) now()->addYear()->format('Y')),
                        TextInput::make('canvas_count')
                            ->label('Canvas Count')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required()
                            ->helperText('Use 2 or more for diptych/triptych/multi-canvas artwork.'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Section::make('Artist Added Classifications')
                    ->schema([
                        TextInput::make('artist_style_name')
                            ->label('Temporary Style')
                            ->disabled(),
                        TextInput::make('artist_subject_style_name')
                            ->label('Temporary Subject Style')
                            ->disabled(),
                        TextInput::make('artist_medium_name')
                            ->label('Temporary Medium')
                            ->disabled(),
                        TextInput::make('artist_size_name')
                            ->label('Temporary Size')
                            ->disabled(),
                    ])
                    ->description('These values were added by the artist. When you approve publish, missing values are added to the admin system and assigned to this artwork.')
                    ->columns(4)
                    ->visible(fn (Get $get): bool => filled($get('artist_style_name'))
                        || filled($get('artist_subject_style_name'))
                        || filled($get('artist_medium_name'))
                        || filled($get('artist_size_name')))
                    ->columnSpanFull(),
                Section::make('BDT Pricing')
                    ->schema([
                        TextInput::make('price')
                            ->label('BDT Regular Price')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('৳')
                            ->placeholder('0.00')
                            ->columnSpan(1),
                        TextInput::make('selling_price')
                            ->label('BDT Sell Price')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('৳')
                            ->placeholder('0.00')
                            ->columnSpan(1),
                        TextInput::make('previous_price')
                            ->label('Previous BDT Regular')
                            ->numeric()
                            ->prefix('৳')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit')
                            ->columnSpan(1),
                        TextInput::make('previous_selling_price')
                            ->label('Previous BDT Sell')
                            ->numeric()
                            ->prefix('৳')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit')
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
                Section::make('USD Pricing')
                    ->schema([
                        TextInput::make('usd_price')
                            ->label('USD Regular Price')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->columnSpan(1),
                        TextInput::make('usd_selling_price')
                            ->label('USD Sell Price')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->columnSpan(1),
                        TextInput::make('previous_usd_price')
                            ->label('Previous USD Regular')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit')
                            ->columnSpan(1),
                        TextInput::make('previous_usd_selling_price')
                            ->label('Previous USD Sell')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit')
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
                Section::make('Status and Version')
                    ->schema([
                        Select::make('status')
                            ->label('Sale Status')
                            ->options([
                                'available' => 'Available',
                                'sold' => 'Sold',
                            ])
                            ->default('available')
                            ->required()
                            ->columnSpan(1),
                        Toggle::make('is_active')
                            ->label('Published on frontend')
                            ->default(true)
                            ->required()
                            ->inline(false)
                            ->columnSpan(2),
                        TextInput::make('price_version')
                            ->label('Price Version')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit')
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Section::make('Picture and Notes')
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Main Picture')
                            ->disk('public')
                            ->directory('artworks')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(51200)
                            ->helperText('Large JPG, PNG, and WebP files are saved as high-quality WebP for faster loading.')
                            ->saveUploadedFileUsing(fn ($file): string => ArtworkImageOptimizer::store($file))
                            ->columnSpanFull(),
                        FileUpload::make('gallery_images')
                            ->label('Artwork Gallery')
                            ->disk('public')
                            ->directory('artworks/gallery')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->reorderable()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(51200)
                            ->helperText('Upload extra angles, close-ups, or separate canvas images for the same artwork.')
                            ->saveUploadedFileUsing(fn ($file): string => ArtworkImageOptimizer::store($file))
                            ->columnSpanFull(),
                        Textarea::make('note')
                            ->label('Condition / note')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function artistCreateForm(): array
    {
        return [
            Section::make('Artist Profile')
                ->schema([
                    TextInput::make('name')
                        ->label('Artist Name')
                        ->required()
                        ->maxLength(255),
                    FileUpload::make('picture_path')
                        ->label('Artist Picture')
                        ->disk('public')
                        ->directory('artists')
                        ->visibility('public')
                        ->image()
                        ->imageEditor()
                        ->maxSize(4096),
                    DatePicker::make('date_of_birth')
                        ->label('Date of Birth')
                        ->native(false),
                    DatePicker::make('date_of_death')
                        ->label('Date of Death')
                        ->native(false),
                    TextInput::make('nationality')
                        ->default('Bangladeshi')
                        ->maxLength(255),
                    TextInput::make('birth_place')
                        ->label('Birth Place')
                        ->maxLength(255),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->inline(false)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Biography')
                ->schema([
                    Textarea::make('biography')
                        ->rows(6)
                        ->columnSpanFull(),
                ]),
        ];
    }

    private static function simpleCreateForm(string $label): array
    {
        return [
            TextInput::make('name')
                ->label($label . ' Name')
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

    private static function sizeCreateForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Size Name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Select::make('unit')
                ->options([
                    'cm' => 'CM',
                    'in' => 'Inch',
                    'mm' => 'MM',
                ])
                ->default('cm')
                ->required(),
            TextInput::make('width')
                ->numeric()
                ->minValue(0)
                ->suffix(fn (Get $get): string => (string) ($get('unit') ?: 'cm')),
            TextInput::make('height')
                ->numeric()
                ->minValue(0)
                ->suffix(fn (Get $get): string => (string) ($get('unit') ?: 'cm')),
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
