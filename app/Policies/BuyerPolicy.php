<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Buyer;
use Illuminate\Auth\Access\HandlesAuthorization;

class BuyerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Buyer');
    }

    public function view(AuthUser $authUser, Buyer $buyer): bool
    {
        return $authUser->can('View:Buyer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Buyer');
    }

    public function update(AuthUser $authUser, Buyer $buyer): bool
    {
        return $authUser->can('Update:Buyer');
    }

    public function delete(AuthUser $authUser, Buyer $buyer): bool
    {
        return $authUser->can('Delete:Buyer');
    }

}