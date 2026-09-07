<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ArtworkTaxRate;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkTaxRatePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArtworkTaxRate');
    }

    public function view(AuthUser $authUser, ArtworkTaxRate $artworkTaxRate): bool
    {
        return $authUser->can('View:ArtworkTaxRate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ArtworkTaxRate');
    }

    public function update(AuthUser $authUser, ArtworkTaxRate $artworkTaxRate): bool
    {
        return $authUser->can('Update:ArtworkTaxRate');
    }

    public function delete(AuthUser $authUser, ArtworkTaxRate $artworkTaxRate): bool
    {
        return $authUser->can('Delete:ArtworkTaxRate');
    }

}