<?php

namespace App\Filament\Resources\Activities\Schemas;

use App\Support\ActivityProperties;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activity')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Date')
                            ->dateTime(),
                        TextEntry::make('description'),
                        TextEntry::make('event')
                            ->label('Action')
                            ->formatStateUsing(fn (?string $state): string => str($state)->headline()->toString())
                            ->badge(),
                        TextEntry::make('properties.module')
                            ->label('Module')
                            ->placeholder('-'),
                        TextEntry::make('properties.record_label')
                            ->label('Record')
                            ->placeholder('-'),
                        TextEntry::make('log_name')
                            ->label('Log')
                            ->placeholder('-'),
                        TextEntry::make('properties.session_duration')
                            ->label('Session time')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Context')
                    ->schema([
                        TextEntry::make('subject_type')
                            ->label('Subject type')
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),
                        TextEntry::make('subject_id')
                            ->label('Subject ID')
                            ->placeholder('-'),
                        TextEntry::make('causer.name')
                            ->label('User')
                            ->placeholder('System'),
                        TextEntry::make('causer_id')
                            ->label('User ID')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('What changed')
                    ->schema([
                        TextEntry::make('properties.details_text')
                            ->label('Details')
                            ->placeholder('-'),
                    ]),
                Section::make('Changes')
                    ->schema([
                        TextEntry::make('properties')
                            ->label('Properties')
                            ->formatStateUsing(fn (mixed $state): string => ActivityProperties::toJson($state))
                            ->fontFamily('mono')
                            ->copyable()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
