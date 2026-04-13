<?php

declare(strict_types=1);

namespace App\Modules\HRIS\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Modules\HRIS\Models\OvertimeRule;
use Illuminate\Auth\Access\HandlesAuthorization;

class OvertimeRulePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OvertimeRule');
    }

    public function view(AuthUser $authUser, OvertimeRule $overtimeRule): bool
    {
        return $authUser->can('View:OvertimeRule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OvertimeRule');
    }

    public function update(AuthUser $authUser, OvertimeRule $overtimeRule): bool
    {
        return $authUser->can('Update:OvertimeRule');
    }

    public function delete(AuthUser $authUser, OvertimeRule $overtimeRule): bool
    {
        return $authUser->can('Delete:OvertimeRule');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:OvertimeRule');
    }

    public function restore(AuthUser $authUser, OvertimeRule $overtimeRule): bool
    {
        return $authUser->can('Restore:OvertimeRule');
    }

    public function forceDelete(AuthUser $authUser, OvertimeRule $overtimeRule): bool
    {
        return $authUser->can('ForceDelete:OvertimeRule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OvertimeRule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OvertimeRule');
    }

    public function replicate(AuthUser $authUser, OvertimeRule $overtimeRule): bool
    {
        return $authUser->can('Replicate:OvertimeRule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OvertimeRule');
    }

    public function approve(AuthUser $authUser, OvertimeRule $overtimeRule): bool
    {
        return $authUser->can('Approve:OvertimeRule');
    }

    public function reject(AuthUser $authUser, OvertimeRule $overtimeRule): bool
    {
        return $authUser->can('Reject:OvertimeRule');
    }

}