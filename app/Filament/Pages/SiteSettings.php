<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\SiteSettings as SiteSettingsCache;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $title = 'Site Settings';

    protected static ?int $navigationSort = 95;

    protected string $view = 'filament.pages.site-settings';

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
        return (bool) auth()->user()?->can('View:SiteSettings');
    }

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->attributesToArray());
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
                Section::make('Branding')
                    ->schema([
                        TextInput::make('site_title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('tagline')
                            ->maxLength(255),
                        FileUpload::make('login_logo_path')
                            ->label('Login logo')
                            ->disk('public')
                            ->directory('site')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048),
                        FileUpload::make('registration_logo_path')
                            ->label('Registration logo')
                            ->disk('public')
                            ->directory('site')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048),
                        FileUpload::make('admin_logo_path')
                            ->label('Dashboard logo')
                            ->disk('public')
                            ->directory('site')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048),
                        FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->disk('public')
                            ->directory('site')
                            ->image()
                            ->maxSize(512),
                    ])
                    ->columns(2),
                Section::make('Frontend Feature Controls')
                    ->description('Turn frontend modules on or off without changing the catalogue data.')
                    ->schema([
                        Toggle::make('virtual_gallery_enabled')
                            ->label('Virtual gallery: show on frontend')
                            ->helperText('On = visible. Off = the gallery menu is hidden and gallery URLs are unavailable.')
                            ->default(true)
                            ->inline(false),
                        Toggle::make('artist_login_enabled')
                            ->label('Artist login and portal: show on frontend')
                            ->helperText('On = visible. Off = login, registration, dashboard, and upload pages are unavailable.')
                            ->default(true)
                            ->inline(false),
                        Toggle::make('exhibition_enabled')->label('Exhibitions: show on frontend')->helperText('On = exhibition menu and pages are available.')->default(true)->inline(false),
                        Toggle::make('events_pr_enabled')->label('Events & PR: show on frontend')->helperText('On = Events & PR menu and pages are available.')->default(true)->inline(false),
                    ])
                    ->columns(2),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255),
                        Textarea::make('meta_description')
                            ->rows(3)
                            ->maxLength(500),
                        Textarea::make('meta_keywords')
                            ->rows(2),
                        Select::make('meta_robots')
                            ->options([
                                'index, follow' => 'Index, follow',
                                'noindex, follow' => 'No index, follow',
                                'index, nofollow' => 'Index, no follow',
                                'noindex, nofollow' => 'No index, no follow',
                            ])
                            ->default('index, follow'),
                        TextInput::make('canonical_url')
                            ->url()
                            ->maxLength(255),
                        FileUpload::make('default_og_image_path')
                            ->label('Default social share image')
                            ->disk('public')
                            ->directory('site')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048),
                    ])
                    ->columns(2),
                Section::make('Analytics and Verification')
                    ->schema([
                        TextInput::make('google_analytics_id')
                            ->label('Google Analytics ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->maxLength(255),
                        TextInput::make('google_tag_manager_id')
                            ->label('Google Tag Manager ID')
                            ->placeholder('GTM-XXXXXXX')
                            ->maxLength(255),
                        TextInput::make('google_site_verification')
                            ->label('Google Search Console verification')
                            ->maxLength(255),
                        TextInput::make('bing_site_verification')
                            ->label('Bing Webmaster verification')
                            ->maxLength(255),
                        TextInput::make('facebook_pixel_id')
                            ->label('Facebook Pixel ID')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Social Media')
                    ->schema([
                        TextInput::make('facebook_url')->url()->maxLength(255),
                        TextInput::make('instagram_url')->url()->maxLength(255),
                        TextInput::make('linkedin_url')->url()->maxLength(255),
                        TextInput::make('youtube_url')->url()->maxLength(255),
                        TextInput::make('x_url')->label('X / Twitter URL')->url()->maxLength(255),
                        TextInput::make('whatsapp_url')->url()->maxLength(255),
                        TextInput::make('whatsapp_phone')
                            ->label('WhatsApp number')
                            ->tel()
                            ->placeholder('+8801XXXXXXXXX')
                            ->helperText('Use the international format without spaces when possible.'),
                        TextInput::make('whatsapp_inquiry_label')
                            ->label('WhatsApp inquiry button label')
                            ->default('WhatsApp Inquiry')
                            ->maxLength(80),
                        Textarea::make('whatsapp_inquiry_template')
                            ->label('WhatsApp inquiry message template')
                            ->rows(5)
                            ->default("Hello ART Story, I am interested in {{ artwork_title }} by {{ artist_name }}.\n\nName: {{ name }}\nWhatsApp: {{ whatsapp }}\nEmail: {{ email }}\nMessage: {{ message }}\n\nArtwork link: {{ artwork_url }}")
                            ->helperText('Available fields: {{ artwork_title }}, {{ artist_name }}, {{ artwork_code }}, {{ name }}, {{ whatsapp }}, {{ email }}, {{ message }}, {{ artwork_url }}.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Contact')
                    ->schema([
                        TextInput::make('contact_email')->label('Contact email')->email()->maxLength(255),
                        TextInput::make('contact_phone')->label('Contact phone number')->tel()->maxLength(255)->helperText('This number appears on the Contact page and in the website footer.'),
                        Textarea::make('head_office_address')->label('Head office address')->rows(3),
                        Textarea::make('new_work_office_address')->label('New work office address')->rows(3),
                        Textarea::make('address')->label('Legacy address')->rows(3)->helperText('Used only as a fallback until you save a Head Office address.'),
                        TextInput::make('contact_eyebrow')->label('Page eyebrow'),
                        TextInput::make('contact_title')->label('Page title'),
                        Textarea::make('contact_description')->label('Page introduction')->rows(3)->columnSpanFull(),
                        TextInput::make('contact_form_title')->label('Form title')->default('Send us a message'),
                        TextInput::make('contact_form_submit_label')->label('Submit button label')->default('Submit Message'),
                        TextInput::make('contact_form_success_message')->label('Success message')->default('Thank you. Your message has been received and our team will be in touch shortly.')->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Footer')
                    ->description('Manage the copy and headings shown across the public website footer. Contact details and social links are managed above.')
                    ->schema([
                        Textarea::make('footer_brand_text')->label('Brand description')->rows(3)->default('Contemporary art for thoughtful spaces, connecting emerging artists with collectors around the globe.')->columnSpanFull(),
                        TextInput::make('footer_sections_heading')->label('Navigation column heading')->default('Sections'),
                        TextInput::make('footer_collectors_heading')->label('Collector column heading')->default('Collectors'),
                        TextInput::make('footer_contact_heading')->label('Contact column heading')->default('Contact'),
                        TextInput::make('footer_copyright_text')->label('Copyright text')->default('All rights reserved.')->helperText('The current year and site title are shown before this text.')->columnSpanFull(),
                        Repeater::make('footer_section_links')
                            ->label('Navigation links')
                            ->schema([
                                TextInput::make('label')->required()->maxLength(80),
                                TextInput::make('url')->label('URL')->required()->maxLength(255),
                            ])
                            ->default([
                                ['label' => 'Artworks', 'url' => '/artworks'],
                                ['label' => 'Exhibition', 'url' => '/exhibitions'],
                                ['label' => 'Events & PR', 'url' => '/upcoming-events'],
                                ['label' => 'About', 'url' => '/about'],
                                ['label' => 'Artists', 'url' => '/artists'],
                                ['label' => 'Contact', 'url' => '/contact'],
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                        Repeater::make('footer_collector_links')
                            ->label('Collector links')
                            ->schema([
                                TextInput::make('label')->required()->maxLength(80),
                                TextInput::make('url')->label('URL')->required()->maxLength(255),
                            ])
                            ->default([
                                ['label' => 'Artwork', 'url' => '/artworks'],
                                ['label' => 'Delivery', 'url' => '/contact'],
                                ['label' => 'Payment', 'url' => '/contact'],
                                ['label' => 'Inquiry', 'url' => '/contact'],
                                ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
                                ['label' => 'Terms & Conditions', 'url' => '/terms-conditions'],
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Legal Pages')
                    ->description('Edit the public Privacy Policy and Terms & Conditions pages. Use the Footer section above to manage their link labels and placement.')
                    ->schema([
                        TextInput::make('privacy_policy_title')->label('Privacy Policy title')->default('Privacy Policy')->required(),
                        RichEditor::make('privacy_policy_content')->label('Privacy Policy content')->default('<p>ART Story respects your privacy and handles personal information responsibly.</p>')->columnSpanFull(),
                        TextInput::make('terms_conditions_title')->label('Terms & Conditions title')->default('Terms & Conditions')->required(),
                        RichEditor::make('terms_conditions_content')->label('Terms & Conditions content')->default('<p>By using ART Story, you agree to use the website and its content responsibly.</p>')->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('About Page')
                    ->schema([
                        TextInput::make('about_eyebrow')->label('Eyebrow'),
                        TextInput::make('about_title')->label('Title'),
                        Textarea::make('about_description')->label('Introduction')->rows(4)->columnSpanFull(),
                        FileUpload::make('about_image_path')->label('About image')->disk('public')->directory('site')->image()->imageEditor(),
                        TextInput::make('about_goal_title')->label('Goal title'),
                        Textarea::make('about_goal_text')->label('Goal text')->rows(4),
                        TextInput::make('about_problem_title')->label('Problem title'),
                        Textarea::make('about_problem_text')->label('Problem text')->rows(4),
                        TextInput::make('about_offer_title')->label('Offer title'),
                        Textarea::make('about_offer_text')->label('Offer text')->rows(4),
                        TextInput::make('about_csr_title')->label('CSR title'),
                        Textarea::make('about_csr_text')->label('CSR text')->rows(4),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        SiteSetting::current()->update($this->form->getState());
        SiteSettingsCache::forget();

        Notification::make()
            ->success()
            ->title('Site settings saved')
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
                        ->label('Save settings')
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                ])->alignment($this->getFormActionsAlignment()),
            ]);
    }
}
