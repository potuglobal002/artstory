<?php

namespace App\Filament\Resources\VirtualGalleryArtworks;

use App\Filament\Resources\VirtualGalleryArtworks\Pages\CreateVirtualGalleryArtwork;
use App\Filament\Resources\VirtualGalleryArtworks\Pages\EditVirtualGalleryArtwork;
use App\Filament\Resources\VirtualGalleryArtworks\Pages\ListVirtualGalleryArtworks;
use App\Models\VirtualGalleryArtwork;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class VirtualGalleryArtworkResource extends Resource
{
    protected static ?string $model = VirtualGalleryArtwork::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Virtual Gallery';

    protected static ?string $navigationLabel = 'Artwork Placements';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Artwork Placement')
                ->schema([
                    Select::make('virtual_gallery_room_id')->relationship('room', 'name')->required()->searchable()->preload()->label('Room'),
                    Select::make('artwork_id')->relationship('artwork', 'title', fn ($query) => $query->where('is_active', true)->with('artist'))
                        ->getOptionLabelFromRecordUsing(fn ($record): string => ($record->title ?: 'Untitled').' - '.($record->artist?->name ?: 'Unknown artist'))
                        ->required()->searchable()->preload(),
                    Select::make('wall')->options(['back' => 'Back wall', 'left' => 'Left wall', 'right' => 'Right wall', 'front' => 'Front wall'])->default('back')->required(),
                    TextInput::make('offset_x')->label('Horizontal Position')->numeric()->default(0)->helperText('Centre is 0. Use negative/positive values to move along the wall.'),
                    TextInput::make('offset_y')->label('Height from Floor')->numeric()->minValue(0)->default(2.5)->required(),
                    TextInput::make('display_width')->label('Frame Width')->numeric()->minValue(0.3)->default(2.2)->required(),
                    TextInput::make('display_height')->label('Frame Height')->numeric()->minValue(0.3)->helperText('Leave blank to preserve the artwork image ratio.'),
                    TextInput::make('rotation_y')->label('Extra Rotation')->numeric()->default(0)->suffix('degrees'),
                    ColorPicker::make('frame_color')->default('#1a1612')->required(),
                    TextInput::make('sort_order')->numeric()->minValue(0)->default(0),
                    Toggle::make('is_active')->label('Visible')->default(true)->inline(false),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('artwork.image_path')->label('Artwork')->disk('public')->square(),
                TextColumn::make('artwork.title')->label('Artwork')->searchable(),
                TextColumn::make('room.gallery.name')->label('Gallery')->searchable(),
                TextColumn::make('room.name')->label('Room')->searchable(),
                TextColumn::make('wall')->badge(),
                IconColumn::make('is_active')->label('Visible')->boolean(),
            ])
            ->filters([SelectFilter::make('wall')->options(['back' => 'Back', 'left' => 'Left', 'right' => 'Right', 'front' => 'Front'])])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVirtualGalleryArtworks::route('/'),
            'create' => CreateVirtualGalleryArtwork::route('/create'),
            'edit' => EditVirtualGalleryArtwork::route('/{record}/edit'),
        ];
    }
}
