<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Models\Event;
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

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Events & PR';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event or PR')->schema([
                TextInput::make('name')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Select::make('content_type')->options(['event' => 'Event', 'pr' => 'PR / News'])->required()->default('event'),
                Textarea::make('excerpt')->rows(3)->columnSpanFull(),
                RichEditor::make('details')->columnSpanFull(),
                FileUpload::make('background_image_path')->label('Background image')->disk('public')->directory('events')->image()->imageEditor(),
                FileUpload::make('gallery_images')->label('Image gallery')->disk('public')->directory('events/gallery')->image()->multiple()->reorderable()->appendFiles()->columnSpanFull(),
                DatePicker::make('start_date'),
                DatePicker::make('end_date')->afterOrEqual('start_date'),
                TextInput::make('location'),
                TextInput::make('external_url')->url(),
                Toggle::make('is_active')->label('Published')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('content_type')->badge(),
            TextColumn::make('start_date')->date()->sortable(),
            TextColumn::make('location')->toggleable(),
            ToggleColumn::make('is_active')->label('Published'),
        ])->recordActions([EditAction::make(), DeleteAction::make()])->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return ['index' => ListEvents::route('/'), 'create' => CreateEvent::route('/create'), 'edit' => EditEvent::route('/{record}/edit')];
    }
}
