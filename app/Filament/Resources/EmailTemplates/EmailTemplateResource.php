<?php

namespace App\Filament\Resources\EmailTemplates;

use App\Filament\Resources\EmailTemplates\Pages\CreateEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\EditEmailTemplate;
use App\Filament\Resources\EmailTemplates\Pages\ListEmailTemplates;
use App\Filament\Resources\EmailTemplates\Pages\ViewEmailTemplate;
use App\Models\EmailTemplate;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Email Templates';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Template')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (?string $state, Set $set): mixed => filled($state) ? $set('key', Str::slug($state, '_')) : null),
                    TextInput::make('key')
                        ->required()
                        ->maxLength(120)
                        ->unique(ignoreRecord: true)
                        ->helperText('Use a clear, stable key in the sender, for example artwork_inquiry_response.'),
                    Select::make('module')
                        ->required()
                        ->searchable()
                        ->options([
                            'ART Story' => 'ART Story',
                            'Course' => 'Course',
                            'General' => 'General',
                        ])
                        ->default('General'),
                    TextInput::make('subject')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),
            Section::make('Body')
                ->description('Blade variables are supported, for example {{ $buyerName }}, {{ $invoiceNumber }}, {{ $candidateName }}, and loops where available.')
                ->schema([
                    Textarea::make('body_html')
                        ->label('HTML body')
                        ->required()
                        ->rows(16)
                        ->columnSpanFull(),
                    Textarea::make('body_text')
                        ->label('Plain text body')
                        ->rows(10)
                        ->columnSpanFull(),
                ]),
            Section::make('Variable reference')
                ->description('Use these variables in subject, HTML body or plain text body.')
                ->schema([
                    Html::make(fn (): HtmlString => new HtmlString(static::variableReferenceHtml())),
                ]),
            Section::make('Available variables')
                ->description('This editable list is saved with the template so admins can document extra variables for future template types.')
                ->schema([
                    KeyValue::make('available_variables')
                        ->label('Variables')
                        ->keyLabel('Variable')
                        ->valueLabel('Description')
                        ->default(fn (): array => static::templateVariables())
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Template')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('key')->copyable(),
                    TextEntry::make('module')->badge(),
                    TextEntry::make('subject'),
                    IconEntry::make('is_active')->boolean(),
                    TextEntry::make('sort_order'),
                ])
                ->columns(2),
            Section::make('Body')
                ->schema([
                    TextEntry::make('body_html')
                        ->label('HTML body')
                        ->prose()
                        ->columnSpanFull(),
                    TextEntry::make('body_text')
                        ->label('Plain text body')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),
            Section::make('Variables')
                ->schema([
                    TextEntry::make('available_variables')
                        ->formatStateUsing(fn ($state): string => collect($state ?: [])->map(function ($value, $key): string {
                            return is_numeric($key) ? (string) $value : "{$key}: {$value}";
                        })->implode(', '))
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('key')->searchable()->copyable(),
                TextColumn::make('module')->badge()->sortable(),
                TextColumn::make('subject')->searchable()->limit(45),
                ToggleColumn::make('is_active')->label('Active'),
                TextColumn::make('sort_order')->sortable(),
                TextColumn::make('updated_at')->label('Updated')->dateTime('M d, Y h:i A')->sortable(),
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
            'index' => ListEmailTemplates::route('/'),
            'create' => CreateEmailTemplate::route('/create'),
            'view' => ViewEmailTemplate::route('/{record}'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }

    private static function variableReferenceHtml(): string
    {
        $rows = collect(static::templateVariables())
            ->map(fn (string $description, string $variable): string => '<tr>'
                . '<td style="padding:9px 12px;border-top:1px solid #e5e7eb;white-space:nowrap;"><code style="background:#fff3ea;color:#c95600;border-radius:6px;padding:3px 6px;">{{ $' . e($variable) . ' }}</code></td>'
                . '<td style="padding:9px 12px;border-top:1px solid #e5e7eb;color:#4b5563;">' . e($description) . '</td>'
                . '</tr>')
            ->implode('');

        return '<div style="display:grid;gap:16px;">'
            . '<div style="overflow-x:auto;border:1px solid #e5e7eb;border-radius:12px;background:#fff;">'
            . '<table style="width:100%;border-collapse:collapse;text-align:left;font-size:14px;">'
            . '<thead style="background:#f9fafb;color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">'
            . '<tr><th style="padding:10px 12px;min-width:210px;">Variable</th><th style="padding:10px 12px;">Use</th></tr>'
            . '</thead><tbody>' . $rows . '</tbody></table></div>'
            . '<div style="border:1px solid #fed7aa;background:#fff7ed;border-radius:12px;padding:14px 16px;">'
            . '<div style="font-weight:700;color:#111827;margin-bottom:8px;">Date and time loop example</div>'
            . '<pre style="white-space:pre-wrap;margin:0;color:#374151;font-size:13px;line-height:1.6;"><code>@foreach ($slotRows as $slot)' . "\n"
            . '- {{ $slot[\'date\'] }}: {{ $slot[\'start_time\'] }} @if($slot[\'end_time\']) - {{ $slot[\'end_time\'] }} @endif' . "\n"
            . '@endforeach</code></pre>'
            . '</div>'
            . '<div style="border:1px solid #dbeafe;background:#eff6ff;border-radius:12px;padding:14px 16px;color:#1f2937;font-size:14px;line-height:1.6;">'
            . '<strong>Tip:</strong> Invoice subject example: <code style="background:#fff;border-radius:6px;padding:3px 6px;">ART Story invoice {{ $invoiceNumber }}</code>'
            . '</div>'
            . '</div>';
    }

    /**
     * @return array<string, string>
     */
    private static function templateVariables(): array
    {
        return static::artistPortalTemplateVariables() + static::artworkInvoiceTemplateVariables() + static::artworkInquiryTemplateVariables();
    }

    /**
     * @return array<string, string>
     */
    private static function artworkInquiryTemplateVariables(): array
    {
        return [
            'customerName' => 'Visitor name',
            'customerEmail' => 'Visitor email address',
            'customerWhatsapp' => 'Visitor WhatsApp number',
            'inquiryMessage' => 'Visitor message',
            'artworkTitle' => 'Artwork title',
            'artworkCode' => 'Artwork code',
            'artistName' => 'Artist name',
            'artworkUrl' => 'Artwork page URL',
            'artworkImageUrl' => 'Artwork image URL',
            'supportEmail' => 'ART Story support email',
            'supportPhone' => 'ART Story support phone',
            'siteTitle' => 'Site title',
            'siteLogoUrl' => 'Site logo URL',
            'inquiry' => 'Full inquiry record for advanced templates',
            'artwork' => 'Full artwork record for advanced templates',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function artistPortalTemplateVariables(): array
    {
        return [
            'artistName' => 'Approved artist name',
            'artistEmail' => 'Artist login email',
            'temporaryPassword' => 'Temporary password for new artist accounts',
            'loginUrl' => 'Frontend artist login page',
            'dashboardUrl' => 'Frontend artist dashboard URL',
            'artist' => 'Full artist record for advanced templates',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function artworkInvoiceTemplateVariables(): array
    {
        return [
            'buyerName' => 'Buyer or customer name',
            'buyerEmail' => 'Buyer email address',
            'buyerPhone' => 'Buyer phone number',
            'buyerDesignation' => 'Buyer designation',
            'invoiceNumber' => 'Artwork invoice number',
            'invoiceDate' => 'Artwork invoice date',
            'artworkTitle' => 'Sold artwork title',
            'artistName' => 'Artwork artist name',
            'artworkYear' => 'Artwork year',
            'artworkStyle' => 'Artwork style',
            'artworkSubjectStyle' => 'Artwork subject style',
            'artworkMedium' => 'Artwork medium',
            'artworkSize' => 'Artwork size label',
            'paymentMethod' => 'Payment method',
            'formattedAmount' => 'Paid amount with currency label',
            'supportPhone' => 'ART Story support phone from Site Settings',
            'supportEmail' => 'ART Story support email from Site Settings',
            'siteTitle' => 'Site title from Site Settings',
            'siteLogoUrl' => 'Logo URL from Site Settings',
            'sale' => 'Full artwork sale record for advanced templates',
            'artwork' => 'Full artwork record for advanced templates',
        ];
    }

}
