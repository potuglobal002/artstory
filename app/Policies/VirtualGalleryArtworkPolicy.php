<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VirtualGalleryArtwork;
use Illuminate\Auth\Access\HandlesAuthorization;

class VirtualGalleryArtworkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VirtualGalleryArtwork');
    }

    public function view(AuthUser $authUser, VirtualGalleryArtwork $virtualGalleryArtwork): bool
    {
        return $authUser->can('View:VirtualGalleryArtwork');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VirtualGalleryArtwork');
    }

    public function update(AuthUser $authUser, VirtualGalleryArtwork $virtualGalleryArtwork): bool
    {
        return $authUser->can('Update:VirtualGalleryArtwork');
    }

    public function delete(AuthUser $authUser, VirtualGalleryArtwork $virtualGalleryArtwork): bool
    {
        return $authUser->can('Delete:VirtualGalleryArtwork');
    }

}