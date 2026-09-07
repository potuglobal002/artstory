<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;

class JourneyItemPolicy
{
    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:JourneyItem'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:JourneyItem'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:JourneyItem'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:JourneyItem'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:JourneyItem'); }
}
