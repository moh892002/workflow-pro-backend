<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttendanceResource;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
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

        return response()->json([
            'success' => true,
            'data' => AttendanceResource::collection($records),
        ]);
    }

    public function today(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $record = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (! $record) {
            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new AttendanceResource($record),
        ]);
    }

    public function checkIn(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $existing = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in today',
            ], 409);
        }

        $isLate = now()->hour > 9 || (now()->hour === 9 && now()->minute > 0);

        $record = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => now(),
            'status' => $isLate ? 'LATE' : 'PRESENT',
        ]);

        return response()->json([
            'success' => true,
            'data' => new AttendanceResource($record),
        ], 201);
    }

    public function checkOut(Request $request, AttendanceRecord $attendanceRecord)
    {
        if ($attendanceRecord->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($attendanceRecord->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked out',
            ], 409);
        }

        $attendanceRecord->update([
            'check_out' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => new AttendanceResource($attendanceRecord),
        ]);
    }

    public function autoCheckIn(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $existing = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'data' => new AttendanceResource($existing),
            ]);
        }

        $isLate = now()->hour > 9 || (now()->hour === 9 && now()->minute > 0);

        $record = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => now(),
            'status' => $isLate ? 'LATE' : 'PRESENT',
        ]);

        return response()->json([
            'success' => true,
            'data' => new AttendanceResource($record),
        ], 201);
    }

    public function autoCheckOut(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $record = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (! $record) {
            return response()->json([
                'success' => false,
                'message' => 'No check-in record found for today',
            ], 404);
        }

        if ($record->check_out) {
            return response()->json([
                'success' => true,
                'data' => new AttendanceResource($record),
            ]);
        }

        $record->update([
            'check_out' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => new AttendanceResource($record),
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();

        $records = AttendanceRecord::where('user_id', $user->id)
            ->latest('date')
            ->paginate($request->input('per_page', 30));

        return response()->json([
            'success' => true,
            'data' => AttendanceResource::collection($records),
        ]);
    }
}
