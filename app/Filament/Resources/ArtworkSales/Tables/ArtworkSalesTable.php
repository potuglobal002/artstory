<?php

namespace App\Filament\Resources\ArtworkSales\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ArtworkSalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sold_at', 'desc')
            ->columns([
                TextColumn::make('artwork_images')
                    ->label('Artwork')
                    ->state(function ($record): string {
                        $items = $record->displayLineItems();
                        $images = $items
                            ->pluck('image_path')
                            ->filter()
                            ->take(3)
                            ->map(function (string $path, int $index): string {
                                $url = e(asset('storage/' . $path));
                                $offset = $index * 18;

                                return '<img src="' . $url . '" alt="" style="position:absolute;left:' . $offset . 'px;top:0;width:54px;height:54px;object-fit:cover;border:2px solid #fff;background:#f8fafc;box-shadow:0 1px 3px rgba(15,23,42,.18);">';
                            })
                            ->implode('');

                        if ($images === '') {
                            return '<div style="width:54px;height:54px;background:#f8fafc;border:1px solid #e2e8f0;"></div>';
                        }

                        $count = $items->count();
                        $badge = $count > 3
                            ? '<span style="position:absolute;left:58px;top:30px;display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:999px;background:#111827;color:#fff;font-size:11px;font-weight:700;border:2px solid #fff;">+' . ($count - 3) . '</span>'
                            : '';

                        return '<div style="position:relative;width:96px;height:56px;">' . $images . $badge . '</div>';
                    })
                    ->html(),
                TextColumn::make('artwork_summary')
                    ->label('Art Details')
                    ->state(fn ($record): string => $record->displayLineItems()
                        ->pluck('title')
                        ->filter()
                        ->implode(', '))
                    ->description(fn ($record): string => ($record->lineItemsLabel()) . ' | ' . $record->displayLineItems()
                        ->pluck('artist_name')
                        ->filter()
                        ->unique()
                        ->implode(', '))
                    ->weight('bold'),
                TextColumn::make('sold_price')
                    ->label('Total Paid')
                    ->state(fn ($record): string => $record->formattedMoney($record->totalPaid()))
                    ->sortable()
                    ->placeholder('-')
                    ->weight('bold'),
                TextColumn::make('tax_summary')
                    ->label('Tax')
                    ->state(fn ($record): string => ((float) ($record->tax_percentage ?? 0)) . '%')
                    ->description(fn ($record): string => $record->formattedMoney($record->tax_amount ?? 0))
                    ->placeholder('-'),
                TextColumn::make('paid_by')
                    ->label('Paid By')
                    ->placeholder('-')
                    ->badge(),
                TextColumn::make('buyer_name')
                    ->label('Buyer')
                    ->description(fn ($record): string => collect([$record->buyer_designation, $record->buyer_phone ?: $record->buyer_email])->filter()->implode(' | ') ?: '-')
                    ->searchable(['buyer_name', 'buyer_designation', 'buyer_phone', 'buyer_email'])
                    ->placeholder('-'),
                TextColumn::make('sold_at')
                    ->label('Sale Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->copyable()
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('last_invoice_email_status')
                    ->label('Email')
                    ->badge()
                    ->placeholder('Not sent'),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                SelectFilter::make('artist')
                    ->relationship('artist', 'name')
                    ->preload()
                    ->searchable(),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                \App\Filament\Resources\ArtworkSales\ArtworkSaleResource::downloadInvoiceAction(),
                \App\Filament\Resources\ArtworkSales\ArtworkSaleResource::sendInvoiceEmailAction(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
