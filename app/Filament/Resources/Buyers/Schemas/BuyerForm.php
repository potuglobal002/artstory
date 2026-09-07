<?php

namespace App\Filament\Resources\Buyers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class BuyerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Buyer Information')
                    ->schema([
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
                            ->required()
                            ->inline(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->maxWidth(Width::FiveExtraLarge)
                    ->columnSpan(['lg' => 8]),
            ]);
    }
}
