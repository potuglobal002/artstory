<?php

namespace App\Filament\Resources\ArtworkPaymentMethods\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class ArtworkPaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Payment Method')
                    ->schema([
                        TextInput::make('name')->required()->unique(ignoreRecord: true)->maxLength(255),
                        Textarea::make('description')->rows(3)->columnSpanFull(),
                        Toggle::make('is_active')->label('Active')->default(true)->required()->inline(false),
                    ])
                    ->columns(1)
                    ->maxWidth(Width::FiveExtraLarge)
                    ->columnSpan(['lg' => 8]),
            ]);
    }
}
