<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Contact Messages';

    protected static ?string $modelLabel = 'Contact Message';

    protected static ?string $pluralModelLabel = 'Contact Messages';

    protected static ?int $navigationSort = 30;

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::query()->where('status', 'new')->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Message')
                ->schema([
                    TextEntry::make('created_at')->label('Received')->dateTime('M d, Y h:i A'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('email')->copyable(),
                    TextEntry::make('subject'),
                    TextEntry::make('message')->columnSpanFull(),
                    TextEntry::make('contacted_at')->dateTime('M d, Y h:i A')->placeholder('-'),
                    TextEntry::make('closed_at')->dateTime('M d, Y h:i A')->placeholder('-'),
                    TextEntry::make('admin_notes')->label('Admin notes')->placeholder('-')->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Received')->dateTime('M d, Y h:i A')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('subject')->searchable()->limit(48),
                TextColumn::make('message')->limit(70)->wrap(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'closed' => 'Closed',
                ]),
            ])
            ->recordActions([
                Action::make('markContacted')
                    ->label('Contacted')
                    ->icon(Heroicon::OutlinedPhone)
                    ->color('warning')
                    ->form([Textarea::make('admin_notes')->label('Notes')->rows(3)])
                    ->action(function (ContactMessage $record, array $data): void {
                        $record->markStatus('contacted');
                        $record->admin_notes = $data['admin_notes'] ?? $record->admin_notes;
                        $record->save();
                    })
                    ->visible(fn (ContactMessage $record): bool => $record->status !== 'closed'),
                Action::make('markClosed')
                    ->label('Close')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->action(function (ContactMessage $record): void {
                        $record->markStatus('closed');
                        $record->save();
                    })
                    ->visible(fn (ContactMessage $record): bool => $record->status !== 'closed'),
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
