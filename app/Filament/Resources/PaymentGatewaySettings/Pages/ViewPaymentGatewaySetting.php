<?php

namespace App\Filament\Resources\PaymentGatewaySettings\Pages;

use App\Filament\Resources\PaymentGatewaySettings\PaymentGatewaySettingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentGatewaySetting extends ViewRecord
{
    protected static string $resource = PaymentGatewaySettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
