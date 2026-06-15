<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreActivityLogRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $logService,
    ) {}

    public function index(Request $request)
    {
        $logs = $this->logService->list($request->user(), $request->only(['per_page']));

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
        $validated = $request->validated();
        $log = $this->logService->log(
            $request->user(),
            $validated['action'],
            $validated['details'] ?? null,
        );

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
