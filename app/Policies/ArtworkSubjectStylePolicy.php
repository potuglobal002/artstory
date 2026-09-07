<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ArtworkSubjectStyle;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkSubjectStylePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArtworkSubjectStyle');
    }

    public function view(AuthUser $authUser, ArtworkSubjectStyle $artworkSubjectStyle): bool
    {
        return $authUser->can('View:ArtworkSubjectStyle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ArtworkSubjectStyle');
    }

    public function update(AuthUser $authUser, ArtworkSubjectStyle $artworkSubjectStyle): bool
    {
        return $authUser->can('Update:ArtworkSubjectStyle');
    }

    public function delete(AuthUser $authUser, ArtworkSubjectStyle $artworkSubjectStyle): bool
    {
        return $authUser->can('Delete:ArtworkSubjectStyle');
    }

}