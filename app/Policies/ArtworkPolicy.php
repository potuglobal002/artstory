<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Artwork;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Artwork');
    }

    public function view(AuthUser $authUser, Artwork $artwork): bool
    {
        return $authUser->can('View:Artwork');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Artwork');
    }

    public function update(AuthUser $authUser, Artwork $artwork): bool
    {
        return $authUser->can('Update:Artwork');
    }

    public function delete(AuthUser $authUser, Artwork $artwork): bool
    {
        return $authUser->can('Delete:Artwork');
    }

}