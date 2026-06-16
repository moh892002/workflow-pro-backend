<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER'])
            || $user->id === $task->assigned_to;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER', 'EMPLOYEE']);
    }

    public function update(User $user, Task $task): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER'])
            || $user->id === $task->assigned_to;
    }

    public function delete(User $user, Task $task): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER'])
            || $user->id === $task->assigned_to;
    }
}
