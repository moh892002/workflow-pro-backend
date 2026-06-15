<?php

namespace App\Policies;

use App\Models\PerformanceReview;
use App\Models\User;

class PerformanceReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PerformanceReview $performanceReview): bool
    {
        return $user->id === $performanceReview->user_id
            || in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PerformanceReview $performanceReview): bool
    {
        return $user->id === $performanceReview->reviewer_id
            || in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }

    public function delete(User $user, PerformanceReview $performanceReview): bool
    {
        return in_array($user->role, ['ADMIN', 'HR_MANAGER']);
    }
}
