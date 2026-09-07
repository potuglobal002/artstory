<?php

namespace App\Filament\Pages;

use App\Models\LandingPage;
use App\Models\Artist;
use App\Models\Artwork;
use App\Support\LandingPageSettings as LandingPageCache;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LandingPageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Landing Page';

    protected static ?string $title = 'Landing Page';

    protected static ?int $navigationSort = 96;

    protected string $view = 'filament.pages.landing-page-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess() && parent::shouldRegisterNavigation();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('View:LandingPageSettings');
    }

    public function mount(): void
    {
        $this->form->fill(array_merge(
            LandingPage::defaults(),
            array_filter(LandingPage::current()->attributesToArray(), fn ($value): bool => $value !== null),
        ));
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->operation('edit')
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Publishing')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Publish landing page')
                            ->default(true),
                        TextInput::make('route_path')
                            ->required()
                            ->maxLength(80)
                            ->helperText('Use / for the homepage or one public path like admissions.'),
                        TextInput::make('top_bar_text')
                            ->maxLength(255),
                        TextInput::make('top_bar_cta_label')
                            ->maxLength(80),
                        TextInput::make('top_bar_cta_url')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Navigation')
                    ->schema([
                        Repeater::make('nav_links')
                            ->schema([
                                TextInput::make('label')->required()->maxLength(80),
                                TextInput::make('url')->required()->maxLength(255),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->addActionLabel('Add navigation link'),
                    ]),
                Section::make('Hero')
                    ->schema([
                        TextInput::make('hero_eyebrow')
                            ->label('Small label')
                            ->maxLength(120),
                        TextInput::make('hero_title')
                            ->label('Hero title')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('hero_subtitle')
                            ->label('ART Story tagline')
                            ->rows(3)
                            ->maxLength(700),
                        TextInput::make('hero_cta_label')
                            ->label('Primary button label')
                            ->maxLength(80),
                        TextInput::make('hero_cta_url')
                            ->label('Primary button URL')
                            ->maxLength(255),
                        TextInput::make('hero_secondary_label')
                            ->label('Secondary button label')
                            ->maxLength(80),
                        TextInput::make('hero_secondary_url')
                            ->label('Secondary button URL')
                            ->maxLength(255),
                        TextInput::make('hero_video_url')
                            ->label('Optional video URL')
                            ->maxLength(255),
                        FileUpload::make('hero_background_path')
                            ->label('Hero background image')
                            ->disk('public')
                            ->directory('landing')
                            ->image()
                            ->imageEditor()
                            ->maxSize(51200),
                    ])
                    ->columns(2),
                Section::make('About Us')
                    ->schema([
                        FileUpload::make('about_image_path')
                            ->label('About section image')
                            ->disk('public')
                            ->directory('landing')
                            ->image()
                            ->imageEditor()
                            ->maxSize(51200)
                            ->columnSpanFull(),
                        TextInput::make('services.0.title')
                            ->label('Heading')
                            ->required()
                            ->maxLength(120),
                        Textarea::make('services.0.description')
                            ->label('Description')
                            ->rows(4)
                            ->maxLength(900),
                        TextInput::make('services.0.accent')
                            ->default('about')
                            ->hidden(),
                    ])
                    ->columns(2),
                Section::make('Our Goal')
                    ->schema([
                        TextInput::make('services.1.title')
                            ->label('Heading')
                            ->required()
                            ->maxLength(120),
                        Textarea::make('services.1.description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(700),
                        TextInput::make('services.1.accent')
                            ->default('goal')
                            ->hidden(),
                    ])
                    ->columns(2),
                Section::make('Problem ART Story Solves')
                    ->schema([
                        TextInput::make('services.2.title')
                            ->label('Heading')
                            ->required()
                            ->maxLength(120),
                        Textarea::make('services.2.description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(900),
                        TextInput::make('services.2.accent')
                            ->default('problem')
                            ->hidden(),
                    ])
                    ->columns(2),
                Section::make('CSR')
                    ->schema([
                        TextInput::make('services.3.title')
                            ->label('Heading')
                            ->required()
                            ->maxLength(120),
                        Textarea::make('services.3.description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(700),
                        TextInput::make('services.3.accent')
                            ->default('csr')
                            ->hidden(),
                    ])
                    ->columns(2),
                Section::make('What We Offer')
                    ->schema([
                        TextInput::make('services.4.title')
                            ->label('Heading')
                            ->required()
                            ->maxLength(120),
                        Textarea::make('services.4.description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(900),
                        TextInput::make('services.4.accent')
                            ->default('offer')
                            ->hidden(),
                    ])
                    ->columns(2),
                Section::make('Featured Artworks')
                    ->schema([
                        Select::make('featured_artwork_ids')
                            ->label('Choose artworks to show')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => Artwork::query()
                                ->with('artist')
                                ->where('is_active', true)
                                ->orderBy('title')
                                ->get()
                                ->mapWithKeys(fn (Artwork $artwork): array => [
                                    $artwork->id => ($artwork->title ?: 'Untitled') . ' - ' . ($artwork->artist?->name ?: 'Unknown artist'),
                                ])
                                ->all())
                            ->helperText('Leave empty to show random active artworks automatically.'),
                        TextInput::make('programs.0.category')
                            ->label('Small label')
                            ->maxLength(80),
                        TextInput::make('programs.0.title')
                            ->label('Heading')
                            ->required()
                            ->maxLength(120),
                        Textarea::make('programs.0.description')
                            ->label('Short note')
                            ->rows(2)
                            ->maxLength(300),
                        TextInput::make('programs.0.url')
                            ->label('Button URL')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Featured Artists')
                    ->schema([
                        Select::make('featured_artist_ids')
                            ->label('Choose artists to show')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => Artist::query()
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->helperText('Leave empty to show active artists automatically.'),
                        TextInput::make('programs.1.category')
                            ->label('Small label')
                            ->maxLength(80),
                        TextInput::make('programs.1.title')
                            ->label('Button label')
                            ->required()
                            ->maxLength(120),
                        Textarea::make('programs.1.description')
                            ->label('Heading')
                            ->rows(2)
                            ->maxLength(300),
                        TextInput::make('programs.1.url')
                            ->label('Button URL')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Exhibition / Upcoming Events Landing Section')
                    ->schema([
                        TextInput::make('exhibition_section.eyebrow')
                            ->label('Small label')
                            ->maxLength(120),
                        TextInput::make('exhibition_section.title')
                            ->label('Heading')
                            ->maxLength(180),
                        Textarea::make('exhibition_section.description')
                            ->label('Short description')
                            ->rows(3)
                            ->maxLength(500),
                        TextInput::make('exhibition_section.exhibition_cta_label')
                            ->label('Exhibition button label')
                            ->maxLength(80),
                        TextInput::make('exhibition_section.event_cta_label')
                            ->label('Event button label')
                            ->maxLength(80),
                        FileUpload::make('exhibition_image_path')
                            ->label('Exhibition section image')
                            ->disk('public')
                            ->directory('landing')
                            ->image()
                            ->imageEditor()
                            ->maxSize(51200),
                    ])
                    ->columns(2),
                Section::make('Upcoming Events')
                    ->schema([
                        Repeater::make('event_section')
                            ->label('Events')
                            ->schema([
                                TextInput::make('date')->label('Day')->maxLength(20),
                                TextInput::make('month')->maxLength(20),
                                TextInput::make('title')->required()->maxLength(160),
                                TextInput::make('location')->maxLength(160),
                                TextInput::make('time')->maxLength(80),
                                Textarea::make('description')->rows(2)->maxLength(400),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->addActionLabel('Add event'),
                    ]),
                Section::make('Optional Stats and Notes')
                    ->schema([
                        Repeater::make('trust_stats')
                            ->label('Trust stats')
                            ->schema([
                                TextInput::make('value')->required()->maxLength(40),
                                TextInput::make('label')->required()->maxLength(120),
                                Textarea::make('description')->rows(2)->maxLength(250),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->addActionLabel('Add trust stat'),
                    ]),
                Section::make('Footer')
                    ->schema([
                        TextInput::make('footer_heading')->maxLength(180),
                        Textarea::make('footer_note')->rows(3)->maxLength(600),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $state['route_path'] = $this->normalizeRoutePath($state['route_path'] ?? '/');

        LandingPage::current()->update($state);
        LandingPageCache::forget();

        Notification::make()
            ->success()
            ->title('Landing page saved')
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    protected function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')
                        ->label('Save landing page')
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                ])->alignment($this->getFormActionsAlignment()),
            ]);
    }

    private function normalizeRoutePath(string $path): string
    {
        $path = trim($path);

        if ($path === '' || $path === '/') {
            return '/';
        }

        return trim($path, '/');
    }
}
