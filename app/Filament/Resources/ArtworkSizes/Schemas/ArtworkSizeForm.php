<?php

namespace App\Filament\Resources\ArtworkSizes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class ArtworkSizeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Size')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(2),
                        Select::make('unit')
                            ->options([
                                'cm' => 'CM',
                                'in' => 'Inch',
                                'mm' => 'MM',
                            ])
                            ->default('cm')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('width')
                            ->numeric()
                            ->minValue(0)
                            ->suffix(fn (Get $get): string => (string) ($get('unit') ?: 'cm'))
                            ->columnSpan(2),
                        TextInput::make('height')
                            ->numeric()
                            ->minValue(0)
                            ->suffix(fn (Get $get): string => (string) ($get('unit') ?: 'cm'))
                            ->columnSpan(2),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required()
                            ->inline(false)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(6)
                    ->maxWidth(Width::FiveExtraLarge)
                    ->columnSpan(['lg' => 8]),
            ]);
    }
}
