<?php

namespace App\Filament\Resources\ArtworkSales\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArtworkSaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sale')
                    ->schema([
                        TextEntry::make('artwork_titles')
                            ->label('Artwork Titles')
                            ->state(fn ($record): string => $record->displayLineItems()
                                ->pluck('title')
                                ->filter()
                                ->implode("\n"))
                            ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                            ->html()
                            ->placeholder('-'),
                        TextEntry::make('artist_names')
                            ->label('Artists')
                            ->state(fn ($record): string => $record->displayLineItems()
                                ->pluck('artist_name')
                                ->filter()
                                ->unique()
                                ->implode("\n"))
                            ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                            ->html()
                            ->placeholder('-'),
                        TextEntry::make('sold_at')->label('Sale Date')->date('M d, Y'),
                        TextEntry::make('currency')->label('Currency')->badge(),
                        TextEntry::make('subtotal_amount')
                            ->label('Subtotal')
                            ->state(fn ($record): string => $record->formattedMoney($record->subtotal()))
                            ->placeholder('-'),
                        TextEntry::make('tax_percentage')
                            ->label('Tax')
                            ->state(fn ($record): string => ((float) ($record->tax_percentage ?? 0)) . '% - ' . $record->formattedMoney($record->tax_amount ?? 0))
                            ->placeholder('-'),
                        TextEntry::make('sold_price')
                            ->label('Total Paid')
                            ->state(fn ($record): string => $record->formattedMoney($record->totalPaid()))
                            ->placeholder('-'),
                        TextEntry::make('tax_country_name')->label('Tax Country')->placeholder('-'),
                        TextEntry::make('paid_by')->label('Paid By')->placeholder('-'),
                        TextEntry::make('invoice_number')->label('Invoice Number')->copyable()->placeholder('-'),
                        TextEntry::make('invoice_sent_at')->label('Invoice Sent At')->dateTime('M d, Y h:i A')->placeholder('-'),
                        TextEntry::make('invoice_email_send_count')->label('Invoice Email Count'),
                        TextEntry::make('last_invoice_email_status')->label('Invoice Email Status')->badge()->placeholder('-'),
                        TextEntry::make('buyer_name')->label('Buyer Name')->placeholder('-'),
                        TextEntry::make('buyer_designation')->label('Designation')->placeholder('-'),
                        TextEntry::make('buyer_phone')->label('Buyer Phone')->placeholder('-'),
                        TextEntry::make('buyer_email')->label('Buyer Email')->placeholder('-'),
                        IconEntry::make('is_active')->label('Active')->boolean(),
                        TextEntry::make('line_items')
                            ->label('Purchased Artworks')
                            ->state(fn ($record): string => $record->displayLineItems()
                                ->map(fn (array $item): string => ($item['title'] ?? 'Untitled') . ' - ' . ($item['artist_name'] ?? 'Unknown Artist') . ' - ' . $record->formattedMoney($item['sold_price'] ?? 0))
                                ->implode("\n"))
                            ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('note')->placeholder('-')->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
