<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;

class SuccessStoryPolicy
{
    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:SuccessStory'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:SuccessStory'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:SuccessStory'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:SuccessStory'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:SuccessStory'); }
}
