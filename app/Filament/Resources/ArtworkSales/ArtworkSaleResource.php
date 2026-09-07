<?php

namespace App\Filament\Resources\ArtworkSales;

use App\Filament\Resources\ArtworkSales\Pages\CreateArtworkSale;
use App\Filament\Resources\ArtworkSales\Pages\EditArtworkSale;
use App\Filament\Resources\ArtworkSales\Pages\ListArtworkSales;
use App\Filament\Resources\ArtworkSales\Pages\ViewArtworkSale;
use App\Filament\Resources\ArtworkSales\Schemas\ArtworkSaleForm;
use App\Filament\Resources\ArtworkSales\Schemas\ArtworkSaleInfolist;
use App\Filament\Resources\ArtworkSales\Tables\ArtworkSalesTable;
use App\Mail\ArtworkSaleInvoiceMail;
use App\Models\ArtworkEmailLog;
use App\Models\ArtworkSale;
use App\Support\ArtworkSaleInvoicePdf;
use App\Support\DynamicMailSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Throwable;
use UnitEnum;

class ArtworkSaleResource extends Resource
{
    protected static ?string $model = ArtworkSale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Artwork Sales';

    protected static ?string $navigationLabel = 'Artwork Sales';

    protected static ?string $modelLabel = 'Artwork Sale';

    protected static ?string $pluralModelLabel = 'Artwork Sales';

    protected static ?string $recordTitleAttribute = 'buyer_name';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return ArtworkSaleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtworkSaleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtworkSalesTable::configure($table);
    }

    public static function downloadInvoiceAction(): Action
    {
        return Action::make('downloadInvoice')
            ->label('Invoice PDF')
            ->icon(Heroicon::OutlinedDocumentArrowDown)
            ->iconButton()
            ->tooltip('Download invoice PDF')
            ->color('gray')
            ->action(fn (ArtworkSale $record) => ArtworkSaleInvoicePdf::download($record));
    }

    public static function sendInvoiceEmailAction(): Action
    {
        return Action::make('sendInvoiceEmail')
            ->label('Send invoice')
            ->icon(Heroicon::OutlinedEnvelope)
            ->iconButton()
            ->tooltip('Email invoice to buyer')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Send invoice email')
            ->modalDescription('Send this invoice PDF to the buyer email address.')
            ->visible(fn (ArtworkSale $record): bool => filled($record->buyer_email))
            ->action(function (ArtworkSale $record): void {
                $mail = new ArtworkSaleInvoiceMail($record);
                $log = ArtworkEmailLog::query()->create([
                    'artwork_sale_id' => $record->id,
                    'template_key' => 'artwork_sale_invoice',
                    'recipient_email' => $record->buyer_email,
                    'subject' => $mail->subjectLine,
                    'status' => 'pending',
                    'payload' => [
                        'invoice_number' => $record->invoice_number,
                        'buyer_name' => $record->buyer_name,
                        'currency' => $record->currency,
                        'subtotal' => $record->subtotal(),
                        'tax_percentage' => (float) ($record->tax_percentage ?? 0),
                        'tax_amount' => (float) ($record->tax_amount ?? 0),
                        'total' => $record->totalPaid(),
                        'artworks' => $record->displayLineItems()->map(fn (array $item): array => [
                            'title' => $item['title'] ?? null,
                            'artist' => $item['artist_name'] ?? null,
                            'sold_price' => $item['sold_price'] ?? null,
                        ])->values()->all(),
                    ],
                ]);

                try {
                    DynamicMailSettings::apply(true);
                    Mail::to($record->buyer_email)->send($mail);
                    $log->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                    $record->update([
                        'invoice_sent_at' => now(),
                        'invoice_email_send_count' => ((int) $record->invoice_email_send_count) + 1,
                        'last_invoice_email_status' => 'sent',
                        'last_invoice_email_error' => null,
                    ]);

                    Notification::make()
                        ->title('Invoice email sent')
                        ->success()
                        ->send();
                } catch (Throwable $exception) {
                    report($exception);
                    $log->update([
                        'status' => 'failed',
                        'error_message' => $exception->getMessage(),
                        'failed_at' => now(),
                    ]);
                    $record->update([
                        'last_invoice_email_status' => 'failed',
                        'last_invoice_email_error' => $exception->getMessage(),
                    ]);

                    Notification::make()
                        ->title('Invoice email was not sent')
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->successNotification(null);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworkSales::route('/'),
            'create' => CreateArtworkSale::route('/create'),
            'view' => ViewArtworkSale::route('/{record}'),
            'edit' => EditArtworkSale::route('/{record}/edit'),
        ];
    }
}
