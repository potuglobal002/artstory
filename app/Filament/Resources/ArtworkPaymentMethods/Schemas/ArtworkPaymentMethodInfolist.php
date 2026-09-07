<?php

namespace App\Filament\Resources\ArtworkPaymentMethods\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArtworkPaymentMethodInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment Method')->schema([
                TextEntry::make('name'),
                TextEntry::make('description')->placeholder('-')->columnSpanFull(),
                IconEntry::make('is_active')->label('Active')->boolean(),
            ])->columns(2),
        ]);
    }
}
