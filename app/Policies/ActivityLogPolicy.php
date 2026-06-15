<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ActivityLog $activityLog): bool
    {
        return $user->id === $activityLog->user_id
            || in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, ActivityLog $activityLog): bool
    {
        return in_array($user->role, ['ADMIN']);
    }
}
