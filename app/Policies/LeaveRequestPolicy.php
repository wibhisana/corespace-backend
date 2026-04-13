<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\HRIS\Models\LeaveRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaveRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:LeaveRequest');
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('View:LeaveRequest');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:LeaveRequest');
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('Update:LeaveRequest');
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('Delete:LeaveRequest');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:LeaveRequest');
    }

    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        // Super Admin & HR Manager: global override
        if ($user->hasAnyRole(['super_admin', 'hr_manager'])) {
            return true;
        }

        // Direct Manager: hanya boleh approve cuti bawahan langsungnya
        return $user->can('Approve:LeaveRequest')
            && $leaveRequest->user?->manager_id === $user->id;
    }

    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->hasAnyRole(['super_admin', 'hr_manager'])) {
            return true;
        }

        return $user->can('Reject:LeaveRequest')
            && $leaveRequest->user?->manager_id === $user->id;
    }
}
