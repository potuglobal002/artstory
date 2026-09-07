<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Artist;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtistPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Artist');
    }

    public function view(AuthUser $authUser, Artist $artist): bool
    {
        return $authUser->can('View:Artist');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Artist');
    }

    public function update(AuthUser $authUser, Artist $artist): bool
    {
        return $authUser->can('Update:Artist');
    }

    public function delete(AuthUser $authUser, Artist $artist): bool
    {
        return $authUser->can('Delete:Artist');
    }

}