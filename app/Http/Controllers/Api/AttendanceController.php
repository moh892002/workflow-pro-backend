<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {}

    public function index(Request $request)
    {
        $query = AttendanceRecord::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        $perPage = $request->input('per_page', 50);
        $records = $query->latest('date')->paginate($perPage);

        return $this->success(AttendanceResource::collection($records));
    }

    public function today(Request $request)
    {
        $record = $this->attendanceService->today($request->user());

        if (! $record) {
            return $this->success(null);
        }

        return $this->success(new AttendanceResource($record));
    }

    public function checkIn(Request $request)
    {
        $record = $this->attendanceService->checkIn($request->user());

        return $this->created(new AttendanceResource($record));
    }

    public function checkOut(Request $request, AttendanceRecord $attendanceRecord)
    {
        $record = $this->attendanceService->checkOut($request->user(), $attendanceRecord);

        return $this->success(new AttendanceResource($record));
    }

    public function autoCheckIn(Request $request)
    {
        $record = $this->attendanceService->autoCheckIn($request->user());

        return $this->created(new AttendanceResource($record));
    }

    public function autoCheckOut(Request $request)
    {
        $record = $this->attendanceService->autoCheckOut($request->user());

        return $this->success(new AttendanceResource($record));
    }

    public function history(Request $request)
    {
        $records = $this->attendanceService->history($request->user(), $request->input('per_page', 30));

        return $this->success(AttendanceResource::collection($records));
    }
}
