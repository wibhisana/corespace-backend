<?php

declare(strict_types=1);

namespace App\Modules\HRIS\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Modules\HRIS\Models\OvertimeRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class OvertimeRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OvertimeRequest');
    }

    public function view(AuthUser $authUser, OvertimeRequest $overtimeRequest): bool
    {
        return $authUser->can('View:OvertimeRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OvertimeRequest');
    }

    public function update(AuthUser $authUser, OvertimeRequest $overtimeRequest): bool
    {
        return $authUser->can('Update:OvertimeRequest');
    }

    public function delete(AuthUser $authUser, OvertimeRequest $overtimeRequest): bool
    {
        return $authUser->can('Delete:OvertimeRequest');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:OvertimeRequest');
    }

    public function restore(AuthUser $authUser, OvertimeRequest $overtimeRequest): bool
    {
        return $authUser->can('Restore:OvertimeRequest');
    }

    public function forceDelete(AuthUser $authUser, OvertimeRequest $overtimeRequest): bool
    {
        return $authUser->can('ForceDelete:OvertimeRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OvertimeRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OvertimeRequest');
    }

    public function replicate(AuthUser $authUser, OvertimeRequest $overtimeRequest): bool
    {
        return $authUser->can('Replicate:OvertimeRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OvertimeRequest');
    }

    public function approve(AuthUser $authUser, OvertimeRequest $overtimeRequest): bool
    {
        return $authUser->can('Approve:OvertimeRequest');
    }

    public function reject(AuthUser $authUser, OvertimeRequest $overtimeRequest): bool
    {
        return $authUser->can('Reject:OvertimeRequest');
    }

}