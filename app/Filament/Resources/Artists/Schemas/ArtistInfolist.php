<?php

namespace App\Filament\Resources\Artists\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArtistInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Artist Profile')
                    ->schema([
                        ImageEntry::make('picture_path')
                            ->label('Picture')
                            ->disk('public')
                            ->height(160)
                            ->placeholder('-'),
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->label('Artist login email')
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->placeholder('-'),
                        TextEntry::make('approval_status')
                            ->label('Portal status')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('user.email')
                            ->label('Linked account')
                            ->placeholder('-'),
                        TextEntry::make('date_of_birth')
                            ->label('Date of birth')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('date_of_death')
                            ->label('Date of death')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('nationality')
                            ->placeholder('-'),
                        TextEntry::make('birth_place')
                            ->label('Birth place')
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 7]),
                Section::make('Biography')
                    ->schema([
                        TextEntry::make('biography')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 5]),
            ]);
    }
}
