<?php

namespace App\Filament\Resources\ArtworkInquiries;

use App\Filament\Resources\ArtworkInquiries\Pages\ListArtworkInquiries;
use App\Filament\Resources\ArtworkInquiries\Pages\ViewArtworkInquiry;
use App\Models\ArtworkInquiry;
use App\Mail\ArtworkInquiryResponseMail;
use App\Models\ArtworkEmailLog;
use App\Support\DynamicMailSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ArtworkInquiryResource extends Resource
{
    protected static ?string $model = ArtworkInquiry::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;
    protected static string|UnitEnum|null $navigationGroup = 'Artwork Sales';
    protected static ?string $navigationLabel = 'Artwork Inquiries';
    protected static ?string $modelLabel = 'Artwork Inquiry';
    protected static ?string $pluralModelLabel = 'Artwork Inquiries';
    protected static ?int $navigationSort = 20;

    public static function getNavigationBadge(): ?string
    {
        $count = ArtworkInquiry::query()->where('status', 'new')->count();

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
            Section::make('Inquiry')
                ->schema([
                    TextEntry::make('created_at')->label('Received')->dateTime('M d, Y h:i A'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('whatsapp')->label('WhatsApp')->copyable(),
                    TextEntry::make('email')->placeholder('-')->copyable(),
                    TextEntry::make('artwork_title')->label('Artwork')->placeholder('-'),
                    TextEntry::make('artwork_code')->label('Artwork code')->placeholder('-'),
                    TextEntry::make('artist_name')->label('Artist')->placeholder('-'),
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
                TextColumn::make('whatsapp')->label('WhatsApp')->searchable()->copyable(),
                TextColumn::make('email')->searchable()->placeholder('-'),
                TextColumn::make('artwork_title')->label('Artwork')->searchable()->description(fn ($record): string => collect([$record->artwork_code, $record->artist_name])->filter()->implode(' | ')),
                TextColumn::make('message')->limit(55)->wrap(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'new' => 'New',
                    'contacted' => 'Contacted',
                    'closed' => 'Closed',
                ]),
            ])
            ->recordActions([
                Action::make('replyByEmail')
                    ->label('Email')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Send inquiry response')
                    ->modalDescription('Send the saved response template to the visitor email address.')
                    ->visible(fn (ArtworkInquiry $record): bool => filled($record->email))
                    ->action(function (ArtworkInquiry $record): void {
                        $mail = new ArtworkInquiryResponseMail($record);
                        $log = ArtworkEmailLog::query()->create([
                            'artwork_inquiry_id' => $record->id,
                            'template_key' => 'artwork_inquiry_response',
                            'recipient_email' => $record->email,
                            'subject' => $mail->subjectLine,
                            'status' => 'pending',
                            'payload' => ['inquiry_id' => $record->id, 'name' => $record->name, 'email' => $record->email, 'artwork_title' => $record->artwork_title],
                        ]);

                        try {
                            DynamicMailSettings::apply(true);
                            Mail::to($record->email)->send($mail);
                            $log->update(['status' => 'sent', 'sent_at' => now()]);
                            $record->markStatus('contacted');
                            $record->save();
                            Notification::make()->title('Inquiry response sent')->success()->send();
                        } catch (Throwable $exception) {
                            report($exception);
                            $log->update(['status' => 'failed', 'error_message' => $exception->getMessage(), 'failed_at' => now()]);
                            Notification::make()->title('Inquiry response was not sent')->body($exception->getMessage())->danger()->send();
                        }
                    }),
                Action::make('markContacted')
                    ->label('Contacted')
                    ->icon(Heroicon::OutlinedPhone)
                    ->color('warning')
                    ->form([Textarea::make('admin_notes')->label('Notes')->rows(3)] )
                    ->action(function (ArtworkInquiry $record, array $data): void {
                        $record->markStatus('contacted');
                        $record->admin_notes = $data['admin_notes'] ?? $record->admin_notes;
                        $record->save();
                    })
                    ->visible(fn (ArtworkInquiry $record): bool => $record->status !== 'closed'),
                Action::make('markClosed')
                    ->label('Close')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->action(function (ArtworkInquiry $record): void {
                        $record->markStatus('closed');
                        $record->save();
                    })
                    ->visible(fn (ArtworkInquiry $record): bool => $record->status !== 'closed'),
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworkInquiries::route('/'),
            'view' => ViewArtworkInquiry::route('/{record}'),
        ];
    }
}
