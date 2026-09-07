<?php

namespace App\Filament\Resources\Exhibitions;

use App\Filament\Resources\Exhibitions\Pages\CreateExhibition;
use App\Filament\Resources\Exhibitions\Pages\EditExhibition;
use App\Filament\Resources\Exhibitions\Pages\ListExhibitions;
use App\Models\Artwork;
use App\Models\Exhibition;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ExhibitionResource extends Resource
{
    protected static ?string $model = Exhibition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Exhibition')->schema([
                TextInput::make('title')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Textarea::make('excerpt')->rows(3)->columnSpanFull(),
                RichEditor::make('description')->columnSpanFull(),
                FileUpload::make('background_image_path')->label('Background image')->disk('public')->directory('exhibitions')->image()->imageEditor(),
                FileUpload::make('gallery_images')->label('Image gallery')->disk('public')->directory('exhibitions/gallery')->image()->multiple()->reorderable()->appendFiles()->columnSpanFull(),
                DatePicker::make('start_date'),
                DatePicker::make('end_date')->afterOrEqual('start_date'),
                Select::make('artworks')->label('Artworks shown')->relationship('artworks', 'title')->multiple()->searchable()->preload()->getOptionLabelFromRecordUsing(fn (Artwork $record) => ($record->title ?: 'Untitled').' - '.$record->artist?->name),
                TextInput::make('embedded_url')->label('Embedded / virtual gallery URL')->url(),
                TextInput::make('virtual_gallery_url')->label('Virtual gallery link')->url(),
                Toggle::make('is_active')->label('Published')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('start_date')->date()->sortable(),
            TextColumn::make('end_date')->date()->sortable(),
            TextColumn::make('artworks_count')->counts('artworks')->label('Artworks'),
            ToggleColumn::make('is_active')->label('Published'),
        ])->recordActions([EditAction::make(), DeleteAction::make()])->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return ['index' => ListExhibitions::route('/'), 'create' => CreateExhibition::route('/create'), 'edit' => EditExhibition::route('/{record}/edit')];
    }
}
