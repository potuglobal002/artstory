<?php

namespace App\Filament\Resources\Artworks\Tables;

use App\Mail\ArtworkPublishedMail;
use App\Models\ArtworkMedium;
use App\Models\ArtworkSize;
use App\Models\ArtworkStyle;
use App\Models\ArtworkSubjectStyle;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class ArtworksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Picture')
                    ->disk('public')
                    ->height(64),
                TextColumn::make('artist.name')
                    ->label('Artist name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('artwork_code')
                    ->label('Code')
                    ->copyable()
                    ->searchable()
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('canvas_count')
                    ->label('Canvases')
                    ->badge()
                    ->sortable(),
                TextColumn::make('size.name')
                    ->label('Size')
                    ->placeholder('-'),
                TextColumn::make('style.name')
                    ->label('Style')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('subjectStyle.name')
                    ->label('Subject Style')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('medium.name')
                    ->label('Medium')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('year')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('price')
                    ->label('Regular Price')
                    ->money('BDT')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('selling_price')
                    ->label('Sell Price')
                    ->money('BDT')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('usd_price')
                    ->label('USD Regular')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('usd_selling_price')
                    ->label('USD Sell')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('price_version')
                    ->label('Version')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str($state)->headline()->toString())
                    ->color(fn (string $state): string => match ($state) {
                        'sold' => 'danger',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('review_status')
                    ->label('Review')
                    ->state(fn ($record): string => $record->submitted_by_artist && ! $record->is_active ? 'In Review' : ($record->is_active ? 'Published' : 'Hidden'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'In Review' => 'warning',
                        'Published' => 'success',
                        default => 'gray',
                    }),
                IconColumn::make('submitted_by_artist')
                    ->label('Artist Upload')
                    ->boolean(),
                TextColumn::make('title')
                    ->label('Artwork Title')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('note')
                    ->limit(60)
                    ->placeholder('-'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('artist')
                    ->relationship('artist', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('style')
                    ->relationship('style', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('subjectStyle')
                    ->relationship('subjectStyle', 'name')
                    ->label('Subject Style')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('medium')
                    ->relationship('medium', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('size')
                    ->relationship('size', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'sold' => 'Sold',
                    ]),
                SelectFilter::make('review_state')
                    ->label('Review state')
                    ->options([
                        'pending_artist_uploads' => 'Pending artist uploads',
                        'published' => 'Published',
                        'hidden' => 'Hidden',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'pending_artist_uploads' => $query
                                ->where('submitted_by_artist', true)
                                ->where('is_active', false),
                            'published' => $query->where('is_active', true),
                            'hidden' => $query->where('is_active', false),
                            default => $query,
                        };
                    }),
                TernaryFilter::make('is_active')
                    ->label('Visibility'),
                TernaryFilter::make('submitted_by_artist')
                    ->label('Artist uploads'),
            ])
            ->recordActions([
                Action::make('approveArtwork')
                    ->label('Approve publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve this artwork?')
                    ->modalDescription('This publishes the artwork on the frontend and marks the review as complete.')
                    ->visible(fn ($record): bool => $record->submitted_by_artist && ! $record->is_active)
                    ->action(function ($record): void {
                        static::applyTemporaryClassifications($record);

                        $record->forceFill([
                            'is_active' => true,
                            'status' => $record->status ?: 'available',
                        ])->save();

                        if (filled($record->artist?->email)) {
                            Mail::to($record->artist->email)->send(new ArtworkPublishedMail($record->fresh(['artist', 'style', 'subjectStyle', 'medium', 'size'])));
                        }

                        Notification::make()
                            ->title('Artwork published')
                            ->body('The artwork is now visible on the frontend and the artist was emailed.')
                            ->success()
                            ->send();
                    }),
                Action::make('printQr')
                    ->label('Print QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('gray')
                    ->url(fn ($record): string => route('artworks.qr.print', ['artworks' => $record->id]))
                    ->openUrlInNewTab()
                    ->visible(fn ($record): bool => filled($record->artwork_code)),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('printQrLabels')
                    ->label('Print selected QR labels')
                    ->icon('heroicon-o-qr-code')
                    ->color('gray')
                    ->action(fn (Collection $records) => redirect()->to(route('artworks.qr.print', [
                        'artworks' => $records->pluck('id')->implode(','),
                    ])))
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    private static function applyTemporaryClassifications($record): void
    {
        if (! $record->artwork_style_id && filled($record->artist_style_name)) {
            $record->artwork_style_id = static::findOrCreateClassification(ArtworkStyle::class, $record->artist_style_name)->id;
        }

        if (! $record->artwork_subject_style_id && filled($record->artist_subject_style_name)) {
            $record->artwork_subject_style_id = static::findOrCreateClassification(ArtworkSubjectStyle::class, $record->artist_subject_style_name)->id;
        }

        if (! $record->artwork_medium_id && filled($record->artist_medium_name)) {
            $record->artwork_medium_id = static::findOrCreateClassification(ArtworkMedium::class, $record->artist_medium_name)->id;
        }

        if (! $record->artwork_size_id && filled($record->artist_size_name)) {
            $record->artwork_size_id = static::findOrCreateSize($record->artist_size_name)->id;
        }
    }

    private static function findOrCreateClassification(string $modelClass, string $name)
    {
        $name = trim($name);

        return $modelClass::query()->firstOrCreate(
            ['name' => $name],
            [
                'description' => 'Added from approved artist submission.',
                'is_active' => true,
            ],
        );
    }

    private static function findOrCreateSize(string $name): ArtworkSize
    {
        $name = trim($name);

        return ArtworkSize::query()->firstOrCreate(
            ['name' => $name],
            [
                'unit' => 'cm',
                'description' => 'Added from approved artist submission.',
                'is_active' => true,
            ],
        );
    }
}
