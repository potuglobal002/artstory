<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ArtworkEmailLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkEmailLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArtworkEmailLog');
    }

    public function view(AuthUser $authUser, ArtworkEmailLog $artworkEmailLog): bool
    {
        return $authUser->can('View:ArtworkEmailLog');
    }

}