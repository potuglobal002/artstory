<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;

class CoursePolicy
{
    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:Course'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:Course'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:Course'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:Course'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:Course'); }
}
