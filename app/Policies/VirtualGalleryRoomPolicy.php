<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VirtualGalleryRoom;
use Illuminate\Auth\Access\HandlesAuthorization;

class VirtualGalleryRoomPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VirtualGalleryRoom');
    }

    public function view(AuthUser $authUser, VirtualGalleryRoom $virtualGalleryRoom): bool
    {
        return $authUser->can('View:VirtualGalleryRoom');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VirtualGalleryRoom');
    }

    public function update(AuthUser $authUser, VirtualGalleryRoom $virtualGalleryRoom): bool
    {
        return $authUser->can('Update:VirtualGalleryRoom');
    }

    public function delete(AuthUser $authUser, VirtualGalleryRoom $virtualGalleryRoom): bool
    {
        return $authUser->can('Delete:VirtualGalleryRoom');
    }

}