<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    public function list(User $user, array $params = []): LengthAwarePaginator
    {
        $query = ActivityLog::with('user');
        $perPage = $params['per_page'] ?? 20;

        if (! in_array($user->role, ['ADMIN', 'HR_MANAGER'])) {
            $query->where('user_id', $user->id);
        }

        return $query->latest()->paginate($perPage);
    }

    public function log(User $user, string $action, ?string $details = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'details' => $details,
        ]);
    }
}
