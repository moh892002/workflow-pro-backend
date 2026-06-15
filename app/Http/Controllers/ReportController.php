<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\AttendanceReportRequest;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {}

    public function attendance(AttendanceReportRequest $request): JsonResponse
    {
        $authUser = $request->user();

        if ($authUser->role === 'HR_MANAGER' && (int) $request->user_id === $authUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'HR Managers cannot generate reports for themselves',
            ], 403);
        }

        $data = $this->reportService->attendance(
            $request->user_id,
            $request->start_date,
            $request->end_date,
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
