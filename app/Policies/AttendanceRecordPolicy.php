<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\User;

class AttendanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }

    public function view(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->id === $attendanceRecord->user_id
            || in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->id === $attendanceRecord->user_id
            || in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }

    public function delete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }
}
