<?php

namespace App\Filament\Resources\SuccessStories;

use App\Filament\Resources\SuccessStories\Pages\CreateSuccessStory;
use App\Filament\Resources\SuccessStories\Pages\EditSuccessStory;
use App\Filament\Resources\SuccessStories\Pages\ListSuccessStories;
use App\Models\SuccessStory;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class SuccessStoryResource extends Resource
{
    protected static ?string $model = SuccessStory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Story')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state)))
                        ->maxLength(255),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                    TextInput::make('course')->maxLength(160),
                    TextInput::make('result')->maxLength(120),
                    TextInput::make('meta')->maxLength(255),
                    Textarea::make('quote')->rows(4)->columnSpanFull(),
                    FileUpload::make('image_path')->label('Story image')->disk('public')->directory('success-stories')->image()->imageEditor()->maxSize(4096),
                    Toggle::make('is_featured')->label('Featured'),
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
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('course')->searchable(),
                TextColumn::make('result')->badge(),
                IconColumn::make('is_featured')->boolean()->label('Featured'),
                ToggleColumn::make('is_active')->label('Published'),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuccessStories::route('/'),
            'create' => CreateSuccessStory::route('/create'),
            'edit' => EditSuccessStory::route('/{record}/edit'),
        ];
    }
}
