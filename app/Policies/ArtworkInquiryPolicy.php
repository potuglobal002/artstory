<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ArtworkInquiry;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArtworkInquiryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ArtworkInquiry');
    }

    public function view(AuthUser $authUser, ArtworkInquiry $artworkInquiry): bool
    {
        return $authUser->can('View:ArtworkInquiry');
    }

    public function update(AuthUser $authUser, ArtworkInquiry $artworkInquiry): bool
    {
        return $authUser->can('Update:ArtworkInquiry');
    }

}