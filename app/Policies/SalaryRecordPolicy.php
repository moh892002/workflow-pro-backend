<?php

namespace App\Policies;

use App\Models\SalaryRecord;
use App\Models\User;

class SalaryRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SalaryRecord $salaryRecord): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER'])
            || $user->id === $salaryRecord->user_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }

    public function update(User $user, SalaryRecord $salaryRecord): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }

    public function delete(User $user, SalaryRecord $salaryRecord): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }
}
