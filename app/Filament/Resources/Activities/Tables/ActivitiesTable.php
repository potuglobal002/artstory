<?php

namespace App\Filament\Resources\Activities\Tables;

use App\Support\ActivityProperties;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('module')
                    ->label('Module')
                    ->state(fn (Activity $record): string => ActivityProperties::get($record->properties, 'module', $record->log_name ?: '-'))
                    ->badge()
                    ->searchable(['log_name', 'subject_type']),
                TextColumn::make('event')
                    ->label('Action')
                    ->state(fn (Activity $record): string => ActivityProperties::get($record->properties, 'action', str($record->event)->headline()->toString()))
                    ->badge()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Record')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('details')
                    ->label('Details')
                    ->state(fn (Activity $record): ?string => ActivityProperties::get($record->properties, 'details_text'))
                    ->wrap()
                    ->limit(90)
                    ->placeholder('-'),
                TextColumn::make('session_duration')
                    ->label('Session time')
                    ->state(fn (Activity $record): ?string => ActivityProperties::get($record->properties, 'session_duration'))
                    ->placeholder('-'),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                    ->searchable(),
                TextColumn::make('causer.name')
                    ->label('User')
                    ->placeholder('System')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Module')
                    ->options(fn (): array => Activity::query()
                        ->whereNotNull('log_name')
                        ->distinct()
                        ->orderBy('log_name')
                        ->pluck('log_name', 'log_name')
                        ->all()),
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'role_attached' => 'Role Assigned',
                        'role_detached' => 'Role Removed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
