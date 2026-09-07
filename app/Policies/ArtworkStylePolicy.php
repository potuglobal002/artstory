<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ArtworkStyle;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkStylePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArtworkStyle');
    }

    public function view(AuthUser $authUser, ArtworkStyle $artworkStyle): bool
    {
        return $authUser->can('View:ArtworkStyle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ArtworkStyle');
    }

    public function update(AuthUser $authUser, ArtworkStyle $artworkStyle): bool
    {
        return $authUser->can('Update:ArtworkStyle');
    }

    public function delete(AuthUser $authUser, ArtworkStyle $artworkStyle): bool
    {
        return $authUser->can('Delete:ArtworkStyle');
    }

}