<?php

namespace App\Filament\Resources\ArtworkSizes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArtworkSizeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Size')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('width')
                            ->placeholder('-'),
                        TextEntry::make('height')
                            ->placeholder('-'),
                        TextEntry::make('unit'),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
