<?php

namespace App\Filament\Resources\JourneyItems;

use App\Filament\Resources\JourneyItems\Pages\CreateJourneyItem;
use App\Filament\Resources\JourneyItems\Pages\EditJourneyItem;
use App\Filament\Resources\JourneyItems\Pages\ListJourneyItems;
use App\Models\JourneyItem;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class JourneyItemResource extends Resource
{
    protected static ?string $model = JourneyItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Inspiring Journey';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Journey milestone')
                ->schema([
                    TextInput::make('year')->required()->maxLength(30),
                    TextInput::make('title')->required()->maxLength(255),
                    Textarea::make('description')->rows(4)->columnSpanFull(),
                    FileUpload::make('image_path')->disk('public')->directory('journey')->image()->imageEditor()->maxSize(4096),
                    Toggle::make('is_active')->label('Published')->default(true),
                    TextInput::make('sort_order')->numeric()->default(0),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')->sortable()->searchable(),
                TextColumn::make('title')->searchable(),
                ToggleColumn::make('is_active')->label('Published'),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJourneyItems::route('/'),
            'create' => CreateJourneyItem::route('/create'),
            'edit' => EditJourneyItem::route('/{record}/edit'),
        ];
    }
}
