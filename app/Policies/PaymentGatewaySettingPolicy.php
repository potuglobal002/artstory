<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PaymentGatewaySetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentGatewaySettingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PaymentGatewaySetting');
    }

    public function view(AuthUser $authUser, PaymentGatewaySetting $paymentGatewaySetting): bool
    {
        return $authUser->can('View:PaymentGatewaySetting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PaymentGatewaySetting');
    }

    public function update(AuthUser $authUser, PaymentGatewaySetting $paymentGatewaySetting): bool
    {
        return $authUser->can('Update:PaymentGatewaySetting');
    }

    public function delete(AuthUser $authUser, PaymentGatewaySetting $paymentGatewaySetting): bool
    {
        return $authUser->can('Delete:PaymentGatewaySetting');
    }

}