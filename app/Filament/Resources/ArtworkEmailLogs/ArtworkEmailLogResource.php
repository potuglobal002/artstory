<?php

namespace App\Filament\Resources\ArtworkEmailLogs;

use App\Filament\Resources\ArtworkEmailLogs\Pages\ListArtworkEmailLogs;
use App\Filament\Resources\ArtworkEmailLogs\Pages\ViewArtworkEmailLog;
use App\Models\ArtworkEmailLog;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ArtworkEmailLogResource extends Resource
{
    protected static ?string $model = ArtworkEmailLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Sales';

    protected static ?string $navigationLabel = 'Email Tracking';

    protected static ?string $modelLabel = 'Artwork Email';

    protected static ?string $pluralModelLabel = 'Artwork Email Tracking';

    protected static ?int $navigationSort = 30;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Email Tracking')
                ->schema([
                    TextEntry::make('sale.invoice_number')->label('Invoice')->copyable()->placeholder('-'),
                    TextEntry::make('sale.buyer_name')->label('Buyer')->placeholder('-'),
                    TextEntry::make('inquiry.name')->label('Inquiry contact')->placeholder('-'),
                    TextEntry::make('inquiry.artwork_title')->label('Inquiry artwork')->placeholder('-'),
                    TextEntry::make('recipient_email')->label('Recipient')->copyable(),
                    TextEntry::make('subject')->placeholder('-'),
                    TextEntry::make('template_key')->label('Template')->badge()->placeholder('-'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('sent_at')->dateTime('M d, Y h:i A')->placeholder('-'),
                    TextEntry::make('failed_at')->dateTime('M d, Y h:i A')->placeholder('-'),
                    TextEntry::make('error_message')->placeholder('-')->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Snapshot')
                ->schema([
                    TextEntry::make('payload')
                        ->formatStateUsing(fn ($state): string => json_encode($state ?: [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '-')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Time')->dateTime('M d, Y h:i A')->sortable(),
                TextColumn::make('sale.invoice_number')->label('Invoice')->searchable()->copyable()->placeholder('-'),
                TextColumn::make('sale.buyer_name')->label('Buyer')->searchable()->placeholder('-'),
                TextColumn::make('inquiry.name')->label('Inquiry contact')->searchable()->placeholder('-'),
                TextColumn::make('recipient_email')->label('Recipient')->searchable()->copyable(),
                TextColumn::make('subject')->searchable()->limit(44)->placeholder('-'),
                TextColumn::make('template_key')->label('Template')->badge()->placeholder('-'),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('sent_at')->label('Sent')->dateTime('M d, Y h:i A')->placeholder('-')->sortable(),
                TextColumn::make('error_message')->label('Error')->limit(50)->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworkEmailLogs::route('/'),
            'view' => ViewArtworkEmailLog::route('/{record}'),
        ];
    }
}
