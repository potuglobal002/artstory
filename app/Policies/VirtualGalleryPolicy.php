<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VirtualGallery;
use Illuminate\Auth\Access\HandlesAuthorization;

class VirtualGalleryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VirtualGallery');
    }

    public function view(AuthUser $authUser, VirtualGallery $virtualGallery): bool
    {
        return $authUser->can('View:VirtualGallery');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VirtualGallery');
    }

    public function update(AuthUser $authUser, VirtualGallery $virtualGallery): bool
    {
        return $authUser->can('Update:VirtualGallery');
    }

    public function delete(AuthUser $authUser, VirtualGallery $virtualGallery): bool
    {
        return $authUser->can('Delete:VirtualGallery');
    }

}