<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ArtworkSize;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkSizePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArtworkSize');
    }

    public function view(AuthUser $authUser, ArtworkSize $artworkSize): bool
    {
        return $authUser->can('View:ArtworkSize');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ArtworkSize');
    }

    public function update(AuthUser $authUser, ArtworkSize $artworkSize): bool
    {
        return $authUser->can('Update:ArtworkSize');
    }

    public function delete(AuthUser $authUser, ArtworkSize $artworkSize): bool
    {
        return $authUser->can('Delete:ArtworkSize');
    }

}