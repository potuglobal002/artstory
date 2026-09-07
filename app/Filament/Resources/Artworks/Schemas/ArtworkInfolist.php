<?php

namespace App\Filament\Resources\Artworks\Schemas;

use App\Models\Artwork;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArtworkInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Artwork')
                    ->schema([
                        ImageEntry::make('image_path')
                            ->label('Picture')
                            ->disk('public')
                            ->height(220)
                            ->placeholder('-'),
                        TextEntry::make('title')
                            ->label('Artwork Title')
                            ->placeholder('-'),
                        TextEntry::make('artist.name')
                            ->label('Artist'),
                        TextEntry::make('artwork_code')
                            ->label('Artwork Code')
                            ->copyable()
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('qr_code')
                            ->label('QR Code')
                            ->state(fn (Artwork $record): string => filled($record->artwork_code)
                                ? '<img src="' . e(route('artworks.qr', $record->artwork_code)) . '" alt="' . e($record->artwork_code) . '" style="max-width:220px;width:100%;height:auto;">'
                                : '-')
                            ->html()
                            ->columnSpanFull(),
                        TextEntry::make('style.name')
                            ->label('Style')
                            ->placeholder('-'),
                        TextEntry::make('subjectStyle.name')
                            ->label('Subject Style')
                            ->placeholder('-'),
                        TextEntry::make('medium.name')
                            ->label('Medium')
                            ->placeholder('-'),
                        TextEntry::make('size.name')
                            ->label('Size')
                            ->placeholder('-'),
                        TextEntry::make('canvas_count')
                            ->label('Canvas Count')
                            ->badge(),
                        ImageEntry::make('gallery_images')
                            ->label('Artwork Gallery')
                            ->disk('public')
                            ->height(120)
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('artist_style_name')
                            ->label('Temporary Style')
                            ->placeholder('-'),
                        TextEntry::make('artist_subject_style_name')
                            ->label('Temporary Subject Style')
                            ->placeholder('-'),
                        TextEntry::make('artist_medium_name')
                            ->label('Temporary Medium')
                            ->placeholder('-'),
                        TextEntry::make('artist_size_name')
                            ->label('Temporary Size')
                            ->placeholder('-'),
                        TextEntry::make('year')
                            ->placeholder('-'),
                        TextEntry::make('price')
                            ->label('Regular Price')
                            ->money('BDT')
                            ->placeholder('-'),
                        TextEntry::make('selling_price')
                            ->label('Sell Price')
                            ->money('BDT')
                            ->placeholder('-'),
                        TextEntry::make('usd_price')
                            ->label('USD Regular Price')
                            ->money('USD')
                            ->placeholder('-'),
                        TextEntry::make('usd_selling_price')
                            ->label('USD Sell Price')
                            ->money('USD')
                            ->placeholder('-'),
                        TextEntry::make('price_version')
                            ->label('Price Version')
                            ->badge(),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => str($state)->headline()->toString())
                            ->color(fn (string $state): string => match ($state) {
                                'sold' => 'danger',
                                default => 'success',
                            }),
                        IconEntry::make('is_active')
                            ->label('Visible')
                            ->boolean(),
                        TextEntry::make('note')
                            ->label('Condition / note')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('price_history')
                            ->label('Previous Price Versions')
                            ->state(fn (Artwork $record): string => $record->priceVersions()
                                ->latest('version')
                                ->limit(5)
                                ->get()
                                ->map(fn ($version): string => 'Version ' . $version->version
                                    . ' - Regular: ' . (blank($version->price) ? '-' : 'BDT ' . number_format((float) $version->price, 2))
                                    . ', Sell: ' . (blank($version->selling_price) ? '-' : 'BDT ' . number_format((float) $version->selling_price, 2))
                                    . ', USD Regular: ' . (blank($version->usd_price) ? '-' : 'USD ' . number_format((float) $version->usd_price, 2))
                                    . ', USD Sell: ' . (blank($version->usd_selling_price) ? '-' : 'USD ' . number_format((float) $version->usd_selling_price, 2)))
                                ->implode("\n"))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
