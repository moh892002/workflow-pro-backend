<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreActivityLogRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ActivityLog::with('user');

        if (! in_array($user->role, ['ADMIN', 'HR_MANAGER'])) {
            $query->where('user_id', $user->id);
        }

        $perPage = $request->input('per_page', 20);
        $logs = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs->map(function ($log) {
                return [
                    'id' => (string) $log->id,
                    'user_id' => (string) $log->user_id,
                    'action' => $log->action,
                    'details' => $log->details,
                    'timestamp' => $log->created_at->toIso8601String(),
                ];
            }),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ]);
    }

    public function store(StoreActivityLogRequest $request)
    {
        $log = ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => $request->validated()['action'],
            'details' => $request->validated()['details'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => (string) $log->id,
                'user_id' => (string) $log->user_id,
                'action' => $log->action,
                'details' => $log->details,
                'timestamp' => $log->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
