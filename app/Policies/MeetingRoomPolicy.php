<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MeetingRoom;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingRoomPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MeetingRoom');
    }

    public function view(AuthUser $authUser, MeetingRoom $meetingRoom): bool
    {
        return $authUser->can('View:MeetingRoom');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MeetingRoom');
    }

    public function update(AuthUser $authUser, MeetingRoom $meetingRoom): bool
    {
        return $authUser->can('Update:MeetingRoom');
    }

    public function delete(AuthUser $authUser, MeetingRoom $meetingRoom): bool
    {
        return $authUser->can('Delete:MeetingRoom');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MeetingRoom');
    }

    public function restore(AuthUser $authUser, MeetingRoom $meetingRoom): bool
    {
        return $authUser->can('Restore:MeetingRoom');
    }

    public function forceDelete(AuthUser $authUser, MeetingRoom $meetingRoom): bool
    {
        return $authUser->can('ForceDelete:MeetingRoom');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MeetingRoom');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MeetingRoom');
    }

    public function replicate(AuthUser $authUser, MeetingRoom $meetingRoom): bool
    {
        return $authUser->can('Replicate:MeetingRoom');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MeetingRoom');
    }

    public function approve(AuthUser $authUser, MeetingRoom $meetingRoom): bool
    {
        return $authUser->can('Approve:MeetingRoom');
    }

    public function reject(AuthUser $authUser, MeetingRoom $meetingRoom): bool
    {
        return $authUser->can('Reject:MeetingRoom');
    }

}