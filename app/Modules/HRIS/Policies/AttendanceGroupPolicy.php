<?php

declare(strict_types=1);

namespace App\Modules\HRIS\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Modules\HRIS\Models\AttendanceGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendanceGroupPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AttendanceGroup');
    }

    public function view(AuthUser $authUser, AttendanceGroup $attendanceGroup): bool
    {
        return $authUser->can('View:AttendanceGroup');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AttendanceGroup');
    }

    public function update(AuthUser $authUser, AttendanceGroup $attendanceGroup): bool
    {
        return $authUser->can('Update:AttendanceGroup');
    }

    public function delete(AuthUser $authUser, AttendanceGroup $attendanceGroup): bool
    {
        return $authUser->can('Delete:AttendanceGroup');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AttendanceGroup');
    }

    public function restore(AuthUser $authUser, AttendanceGroup $attendanceGroup): bool
    {
        return $authUser->can('Restore:AttendanceGroup');
    }

    public function forceDelete(AuthUser $authUser, AttendanceGroup $attendanceGroup): bool
    {
        return $authUser->can('ForceDelete:AttendanceGroup');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AttendanceGroup');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AttendanceGroup');
    }

    public function replicate(AuthUser $authUser, AttendanceGroup $attendanceGroup): bool
    {
        return $authUser->can('Replicate:AttendanceGroup');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AttendanceGroup');
    }

    public function approve(AuthUser $authUser, AttendanceGroup $attendanceGroup): bool
    {
        return $authUser->can('Approve:AttendanceGroup');
    }

    public function reject(AuthUser $authUser, AttendanceGroup $attendanceGroup): bool
    {
        return $authUser->can('Reject:AttendanceGroup');
    }

}