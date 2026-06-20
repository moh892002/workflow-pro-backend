<?php

namespace App\Services;

use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class PerformanceReviewService
{
    public function all(User $user): Collection
    {
        if (in_array($user->role, ['ADMIN', 'HR_MANAGER'])) {
            return PerformanceReview::with(['user', 'reviewer'])->get();
        }

        return PerformanceReview::where('user_id', $user->id)
            ->with(['user', 'reviewer'])
            ->get();
    }

    public function create(User $user, array $data): PerformanceReview
    {
        $data['reviewer_id'] ??= $user->id;

        return PerformanceReview::create($data);
    }
}
