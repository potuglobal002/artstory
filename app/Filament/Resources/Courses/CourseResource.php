<?php

namespace App\Filament\Resources\Courses;

use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\Pages\ListCourses;
use App\Filament\Resources\Courses\Pages\ViewCourse;
use App\Models\Course;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
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

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Course')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state)))
                        ->maxLength(255),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                    TextInput::make('category')->maxLength(120),
                    TextInput::make('badge')->maxLength(80),
                    TextInput::make('duration')->maxLength(80),
                    TextInput::make('sessions')->maxLength(80),
                    TextInput::make('fee')->maxLength(80),
                    TextInput::make('fee_amount')
                        ->label('Course fee amount')
                        ->numeric()
                        ->prefix('BDT')
                        ->minValue(0),
                    TextInput::make('fee_note')->maxLength(160),
                    TextInput::make('target_score')->label('Target score')->maxLength(80),
                    TextInput::make('batch_size')->maxLength(80),
                    TextInput::make('campus')->maxLength(160),
                    Textarea::make('excerpt')->rows(3)->columnSpanFull(),
                    Textarea::make('who_for')->rows(3)->columnSpanFull(),
                    Textarea::make('lead')->rows(3)->columnSpanFull(),
                    RichEditor::make('description')->columnSpanFull(),
                    FileUpload::make('image_path')
                        ->label('Course image')
                        ->disk('public')
                        ->directory('courses')
                        ->image()
                        ->imageEditor()
                        ->maxSize(4096),
                    Toggle::make('is_featured')->label('Featured'),
                    Toggle::make('is_active')->label('Published')->default(true),
                    TextInput::make('sort_order')->numeric()->default(0),
                ])
                ->columns(2),
            Section::make('Course Details')
                ->schema([
                    Repeater::make('features')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(160),
                            Textarea::make('description')->rows(2)->maxLength(350),
                        ])
                        ->columns(2)
                        ->collapsible(),
                    Repeater::make('outcomes')
                        ->schema([
                            TextInput::make('text')->required()->maxLength(255),
                        ])
                        ->collapsible(),
                    Repeater::make('includes')
                        ->schema([
                            TextInput::make('text')->required()->maxLength(255),
                        ])
                        ->collapsible(),
                    Repeater::make('modules')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(160),
                            Textarea::make('description')->rows(2)->maxLength(500),
                        ])
                        ->columns(2)
                        ->collapsible(),
                    Repeater::make('schedule')
                        ->schema([
                            TextInput::make('batch')->required()->maxLength(120),
                            TextInput::make('days')->maxLength(120),
                            TextInput::make('time')->maxLength(120),
                        ])
                        ->columns(3)
                        ->collapsible(),
                    Repeater::make('faq')
                        ->schema([
                            TextInput::make('question')->required()->maxLength(255),
                            Textarea::make('answer')->rows(3)->maxLength(700),
                        ])
                        ->columns(2)
                        ->collapsible(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category')->badge()->searchable(),
                TextColumn::make('duration'),
                TextColumn::make('fee_amount')->money('BDT')->label('Fee')->sortable(),
                IconColumn::make('is_featured')->boolean()->label('Featured'),
                ToggleColumn::make('is_active')->label('Published'),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'view' => ViewCourse::route('/{record}'),
            'edit' => EditCourse::route('/{record}/edit'),
        ];
    }
}
