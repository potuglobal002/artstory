<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CourseEnrollment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseEnrollmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CourseEnrollment');
    }

    public function view(AuthUser $authUser, CourseEnrollment $courseEnrollment): bool
    {
        return $authUser->can('View:CourseEnrollment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CourseEnrollment');
    }

    public function update(AuthUser $authUser, CourseEnrollment $courseEnrollment): bool
    {
        return $authUser->can('Update:CourseEnrollment');
    }

    public function delete(AuthUser $authUser, CourseEnrollment $courseEnrollment): bool
    {
        return $authUser->can('Delete:CourseEnrollment');
    }

}