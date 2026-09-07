<?php

namespace App\Filament\Pages;

use App\Models\MailSetting;
use App\Support\DynamicMailSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use SensitiveParameter;
use Throwable;
use UnitEnum;

class EmailSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Email Settings';

    protected static ?string $title = 'Email Settings';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.pages.email-settings';

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess() && parent::shouldRegisterNavigation();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('View:EmailSettings');
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = MailSetting::current();

        $this->form->fill([
            'is_active' => $settings->is_active,
            'mailer' => $settings->mailer,
            'host' => $settings->host,
            'port' => $settings->port,
            'scheme' => $settings->scheme,
            'username' => $settings->username,
            'password' => null,
            'from_address' => $settings->from_address,
            'from_name' => $settings->from_name,
            'ehlo_domain' => $settings->ehlo_domain,
            'timeout' => $settings->timeout ?: 8,
        ]);
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
                Section::make('SMTP Server')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Use dashboard email settings')
                            ->default(true),
                        Select::make('mailer')
                            ->options([
                                'smtp' => 'SMTP',
                                'log' => 'Log only',
                            ])
                            ->default('smtp')
                            ->required(),
                        TextInput::make('host')
                            ->label('SMTP host')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('port')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(65535),
                        Select::make('scheme')
                            ->label('Security')
                            ->options([
                                'smtps' => 'SSL / SMTPS',
                                'tls' => 'TLS / STARTTLS',
                                '' => 'None',
                            ])
                            ->default('smtps'),
                        TextInput::make('ehlo_domain')
                            ->label('EHLO domain')
                            ->maxLength(255),
                        TextInput::make('timeout')
                            ->label('SMTP timeout seconds')
                            ->numeric()
                            ->minValue(3)
                            ->maxValue(60)
                            ->default(8)
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Credentials')
                    ->schema([
                        TextInput::make('username')
                            ->label('SMTP username')
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('SMTP password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('Leave blank to keep the current password.'),
                    ])
                    ->columns(2),
                Section::make('Sender')
                    ->schema([
                        TextInput::make('from_address')
                            ->label('From email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('from_name')
                            ->label('From name')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = MailSetting::current();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $settings->update($data);
        Cache::forget('settings.mail');
        DynamicMailSettings::apply(purgeResolvedMailers: true);
        $this->data['password'] = null;

        Notification::make()
            ->success()
            ->title('Email settings saved')
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTestEmail')
                ->label('Send test email')
                ->icon(Heroicon::PaperAirplane)
                ->schema([
                    TextInput::make('recipient')
                        ->label('Recipient email')
                        ->email()
                        ->required()
                        ->default(fn (): ?string => auth()->user()?->email),
                ])
                ->action(function (#[SensitiveParameter] array $data): void {
                    DynamicMailSettings::apply(purgeResolvedMailers: true);

                    try {
                        Mail::raw('This is a test email from ART Story dashboard email settings.', function ($message) use ($data): void {
                            $message
                                ->to($data['recipient'])
                                ->subject('ART Story email settings test');
                        });
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->danger()
                            ->title('Test email failed')
                            ->body($exception->getMessage())
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->success()
                        ->title('Test email sent')
                        ->send();
                }),
        ];
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
                ])
                    ->alignment($this->getFormActionsAlignment()),
            ]);
    }
}
