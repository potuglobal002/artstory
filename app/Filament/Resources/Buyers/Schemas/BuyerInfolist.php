<?php

namespace App\Filament\Resources\Buyers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BuyerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Buyer Information')
                    ->schema([
                        TextEntry::make('name')->label('Buyer Name'),
                        TextEntry::make('designation')->placeholder('-'),
                        TextEntry::make('phone')->placeholder('-'),
                        TextEntry::make('email')->placeholder('-'),
                        TextEntry::make('address')->placeholder('-')->columnSpanFull(),
                        TextEntry::make('note')->placeholder('-')->columnSpanFull(),
                        IconEntry::make('is_active')->label('Active')->boolean(),
                    ])
                    ->columns(2),
            ]);
    }
}
