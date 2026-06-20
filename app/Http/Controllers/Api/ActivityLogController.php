<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        return $this->success($logs->map(function ($log) {
            return [
                'id' => (string) $log->id,
                'user_id' => (string) $log->user_id,
                'action' => $log->action,
                'details' => $log->details,
                'timestamp' => $log->created_at->toIso8601String(),
            ];
        }));
    }

    public function store(StoreActivityLogRequest $request)
    {
        $validated = $request->validated();

        $log = $this->logService->log(
            $request->user(),
            $validated['action'],
            $validated['details'] ?? null,
        );

        return $this->created([
            'id' => (string) $log->id,
            'user_id' => (string) $log->user_id,
            'action' => $log->action,
            'details' => $log->details,
            'timestamp' => $log->created_at->toIso8601String(),
        ]);
    }
}
