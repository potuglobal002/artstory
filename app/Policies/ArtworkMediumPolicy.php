<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ArtworkMedium;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkMediumPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArtworkMedium');
    }

    public function view(AuthUser $authUser, ArtworkMedium $artworkMedium): bool
    {
        return $authUser->can('View:ArtworkMedium');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ArtworkMedium');
    }

    public function update(AuthUser $authUser, ArtworkMedium $artworkMedium): bool
    {
        return $authUser->can('Update:ArtworkMedium');
    }

    public function delete(AuthUser $authUser, ArtworkMedium $artworkMedium): bool
    {
        return $authUser->can('Delete:ArtworkMedium');
    }

}