<?php

namespace App\Filament\Resources\PaymentGatewaySettings;

use App\Filament\Resources\PaymentGatewaySettings\Pages\CreatePaymentGatewaySetting;
use App\Filament\Resources\PaymentGatewaySettings\Pages\EditPaymentGatewaySetting;
use App\Filament\Resources\PaymentGatewaySettings\Pages\ListPaymentGatewaySettings;
use App\Filament\Resources\PaymentGatewaySettings\Pages\ViewPaymentGatewaySetting;
use App\Models\PaymentGatewaySetting;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
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
use UnitEnum;

class PaymentGatewaySettingResource extends Resource
{
    protected static ?string $model = PaymentGatewaySetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|UnitEnum|null $navigationGroup = 'Payments';

    protected static ?string $navigationLabel = 'Payment Gateways';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Gateway')
                ->schema([
                    TextInput::make('name')->required()->maxLength(120),
                    Select::make('code')
                        ->options([
                            'bkash' => 'bKash Merchant',
                            'amarpay' => 'aamarPay',
                            'shurjopay' => 'ShurjoPay',
                        ])
                        ->required(),
                    Toggle::make('is_active')->label('Active')->default(true),
                    Toggle::make('is_sandbox')->label('Sandbox mode')->default(true),
                    Toggle::make('use_sandbox_simulator')
                        ->label('Use local sandbox simulator')
                        ->helperText('Keep enabled until real sandbox merchant credentials are approved.'),
                    TextInput::make('currency')->default('BDT')->maxLength(3),
                    TextInput::make('logo_url')->label('Logo URL')->url()->maxLength(255),
                    TextInput::make('sort_order')->numeric()->default(0),
                ])
                ->columns(2),
            Section::make('Merchant Credentials')
                ->schema([
                    TextInput::make('merchant_id')->maxLength(255),
                    TextInput::make('store_id')->maxLength(255),
                    TextInput::make('username')->maxLength(255),
                    TextInput::make('password')->password()->revealable()->dehydrated(fn (?string $state): bool => filled($state)),
                    TextInput::make('api_key')->password()->revealable()->dehydrated(fn (?string $state): bool => filled($state)),
                    TextInput::make('api_secret')->label('Signature key / API secret')->password()->revealable()->dehydrated(fn (?string $state): bool => filled($state)),
                ])
                ->columns(2),
            Section::make('Endpoints')
                ->schema([
                    TextInput::make('base_url')->url()->maxLength(255),
                    TextInput::make('checkout_url')->url()->maxLength(255),
                    KeyValue::make('extra_config')->label('Extra config'),
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('code')->badge(),
                IconColumn::make('is_sandbox')->boolean()->label('Sandbox'),
                IconColumn::make('use_sandbox_simulator')->boolean()->label('Simulator'),
                ToggleColumn::make('is_active')->label('Active'),
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
            'index' => ListPaymentGatewaySettings::route('/'),
            'create' => CreatePaymentGatewaySetting::route('/create'),
            'view' => ViewPaymentGatewaySetting::route('/{record}'),
            'edit' => EditPaymentGatewaySetting::route('/{record}/edit'),
        ];
    }
}
