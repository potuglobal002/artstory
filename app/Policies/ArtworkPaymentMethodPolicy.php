<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ArtworkPaymentMethod;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkPaymentMethodPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArtworkPaymentMethod');
    }

    public function view(AuthUser $authUser, ArtworkPaymentMethod $artworkPaymentMethod): bool
    {
        return $authUser->can('View:ArtworkPaymentMethod');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ArtworkPaymentMethod');
    }

    public function update(AuthUser $authUser, ArtworkPaymentMethod $artworkPaymentMethod): bool
    {
        return $authUser->can('Update:ArtworkPaymentMethod');
    }

    public function delete(AuthUser $authUser, ArtworkPaymentMethod $artworkPaymentMethod): bool
    {
        return $authUser->can('Delete:ArtworkPaymentMethod');
    }

}