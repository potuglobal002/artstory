<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ArtworkSale;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkSalePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArtworkSale');
    }

    public function view(AuthUser $authUser, ArtworkSale $artworkSale): bool
    {
        return $authUser->can('View:ArtworkSale');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ArtworkSale');
    }

    public function update(AuthUser $authUser, ArtworkSale $artworkSale): bool
    {
        return $authUser->can('Update:ArtworkSale');
    }

    public function delete(AuthUser $authUser, ArtworkSale $artworkSale): bool
    {
        return $authUser->can('Delete:ArtworkSale');
    }

}