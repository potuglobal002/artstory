<?php

namespace App\Filament\Resources\VirtualGalleries;

use App\Filament\Resources\VirtualGalleries\Pages\CreateVirtualGallery;
use App\Filament\Resources\VirtualGalleries\Pages\EditVirtualGallery;
use App\Filament\Resources\VirtualGalleries\Pages\ListVirtualGalleries;
use App\Models\VirtualGallery;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class VirtualGalleryResource extends Resource
{
    protected static ?string $model = VirtualGallery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Virtual Gallery';

    protected static ?string $navigationLabel = 'Galleries';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Gallery')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255)->live(onBlur: true),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255)->helperText('Used in the public gallery URL.'),
                    Textarea::make('description')->rows(4)->columnSpanFull(),
                    FileUpload::make('cover_image_path')->label('Cover Image')->image()->directory('virtual-galleries/covers')->imageEditor(),
                    Toggle::make('auto_include_available_artworks')
                        ->label('Automatically show available artworks')
                        ->helperText('Newly available catalogue artworks appear in the public gallery automatically.')
                        ->default(true)
                        ->live()
                        ->inline(false),
                    TextInput::make('artworks_per_room')
                        ->label('Artworks per room')
                        ->numeric()
                        ->minValue(6)
                        ->maxValue(30)
                        ->default(18)
                        ->visible(fn ($get): bool => (bool) $get('auto_include_available_artworks')),
                    TextInput::make('sort_order')->numeric()->default(0)->minValue(0),
                    Toggle::make('is_active')->label('Published')->default(false)->inline(false),
                ])
                ->columns(2),
            Section::make('Automatic Gallery Design')
                ->description('Controls the public room design when automatic catalogue mode is enabled.')
                ->visible(fn ($get): bool => (bool) $get('auto_include_available_artworks'))
                ->schema([
                    ColorPicker::make('automatic_wall_color')->label('Wall color')->default('#e4e5e7')->required(),
                    ColorPicker::make('automatic_floor_color')->label('Floor color')->default('#b5b7b9')->required(),
                    ColorPicker::make('automatic_ceiling_color')->label('Ceiling color')->default('#62656c')->required(),
                    TextInput::make('automatic_room_width')->label('Room width')->numeric()->minValue(14)->maxValue(60)->default(28)->required(),
                    TextInput::make('automatic_room_depth')->label('Room depth')->numeric()->minValue(14)->maxValue(60)->default(26)->required(),
                    TextInput::make('automatic_room_height')->label('Room height')->numeric()->minValue(3)->maxValue(16)->default(7.2)->required(),
                    TextInput::make('automatic_camera_x')->label('Starting camera X')->numeric()->default(0)->required(),
                    TextInput::make('automatic_camera_y')->label('Starting camera height')->numeric()->minValue(1)->maxValue(8)->default(2.05)->required(),
                    TextInput::make('automatic_camera_z')->label('Starting camera Z')->numeric()->default(10.5)->required(),
                    Toggle::make('automatic_show_partitions')->label('Show internal exhibition walls')->default(true)->inline(false),
                    Toggle::make('automatic_show_floor_grid')->label('Show tiled floor grid')->default(true)->inline(false),
                    Toggle::make('automatic_use_realistic_environment')
                        ->label('Use realistic 360 gallery environment')
                        ->helperText('Uses the polished gallery environment on the public exhibition.')
                        ->default(true)
                        ->live()
                        ->inline(false),
                    FileUpload::make('automatic_environment_path')
                        ->label('Custom 360 environment')
                        ->helperText('Optional 2:1 panoramic image. Leave blank to use the curated gallery environment.')
                        ->image()
                        ->directory('virtual-galleries/environments')
                        ->visible(fn ($get): bool => (bool) $get('automatic_use_realistic_environment')),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('cover_image_path')->label('Cover')->disk('public')->square(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('rooms_count')->counts('rooms')->label('Rooms'),
                IconColumn::make('auto_include_available_artworks')->label('Auto catalogue')->boolean(),
                IconColumn::make('is_active')->label('Published')->boolean(),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVirtualGalleries::route('/'),
            'create' => CreateVirtualGallery::route('/create'),
            'edit' => EditVirtualGallery::route('/{record}/edit'),
        ];
    }
}
