<?php

namespace App\Filament\Resources\VirtualGalleryRooms;

use App\Filament\Resources\VirtualGalleryRooms\Pages\CreateVirtualGalleryRoom;
use App\Filament\Resources\VirtualGalleryRooms\Pages\EditVirtualGalleryRoom;
use App\Filament\Resources\VirtualGalleryRooms\Pages\ListVirtualGalleryRooms;
use App\Models\VirtualGalleryRoom;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class VirtualGalleryRoomResource extends Resource
{
    protected static ?string $model = VirtualGalleryRoom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Virtual Gallery';

    protected static ?string $navigationLabel = 'Rooms';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Room')
                ->schema([
                    Select::make('virtual_gallery_id')->relationship('gallery', 'name')->required()->searchable()->preload(),
                    TextInput::make('name')->required()->maxLength(255),
                    Textarea::make('description')->rows(3)->columnSpanFull(),
                    FileUpload::make('panorama_path')->label('360 Panorama')->image()->directory('virtual-galleries/panoramas')->helperText('Optional equirectangular 360 image for an immersive background.')->columnSpanFull(),
                    ColorPicker::make('wall_color')->default('#e8e3d8')->required(),
                    ColorPicker::make('floor_color')->default('#4b4038')->required(),
                    ColorPicker::make('ceiling_color')->default('#f7f5f0')->required(),
                    TextInput::make('width')->numeric()->minValue(4)->default(12)->required(),
                    TextInput::make('depth')->numeric()->minValue(4)->default(10)->required(),
                    TextInput::make('height')->numeric()->minValue(2.5)->default(5)->required(),
                    TextInput::make('camera_x')->label('Camera X')->numeric()->default(0)->required(),
                    TextInput::make('camera_y')->label('Camera Height')->numeric()->minValue(1)->default(1.7)->required(),
                    TextInput::make('camera_z')->label('Camera Z')->numeric()->default(3.8)->required(),
                    TextInput::make('sort_order')->numeric()->minValue(0)->default(0),
                    Toggle::make('is_active')->label('Published')->default(true)->inline(false),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('gallery.name')->label('Gallery')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('placements_count')->counts('placements')->label('Artworks'),
                IconColumn::make('is_active')->label('Published')->boolean(),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([SelectFilter::make('virtual_gallery_id')->relationship('gallery', 'name')->label('Gallery')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVirtualGalleryRooms::route('/'),
            'create' => CreateVirtualGalleryRoom::route('/create'),
            'edit' => EditVirtualGalleryRoom::route('/{record}/edit'),
        ];
    }
}
