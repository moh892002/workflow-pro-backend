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
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SalaryRecord $salaryRecord): bool
    {
        return true;
    }

    public function delete(User $user, SalaryRecord $salaryRecord): bool
    {
        return true;
    }
}
