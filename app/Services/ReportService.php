<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;

class ReportService
{
    public function attendance(int $userId, string $startDate, string $endDate): array
    {
        $user = User::with('department')->findOrFail($userId);
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $records = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        $totalDays = $start->diffInDays($end) + 1;
        $presentDays = $records->count();
        $absentDays = max(0, $totalDays - $presentDays);

        $totalHours = 0;
        $lateDays = 0;

        $dailyBreakdown = $records->map(function ($record) use (&$totalHours, &$lateDays) {
            $checkIn = $record->check_in ? Carbon::parse($record->check_in) : null;
            $checkOut = $record->check_out ? Carbon::parse($record->check_out) : null;

            $hours = null;
            if ($checkIn && $checkOut) {
                $hours = round($checkOut->diffInMinutes($checkIn) / 60, 2);
                $totalHours += $hours;
            }

            $isLate = false;
            if ($checkIn) {
                $isLate = $checkIn->hour > 9 || ($checkIn->hour === 9 && $checkIn->minute > 0);
                if ($isLate) {
                    $lateDays++;
                }
            }

            return [
                'date' => $record->date->toDateString(),
                'check_in' => $checkIn?->toIso8601String(),
                'check_out' => $checkOut?->toIso8601String(),
                'working_hours' => $hours,
                'status' => $isLate ? 'LATE' : 'PRESENT',
            ];
        });

        $averageHours = $presentDays > 0 ? round($totalHours / $presentDays, 2) : 0;

        return [
            'employee' => [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'department' => $user->department?->name,
            ],
            'period' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
            'stats' => [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'total_hours' => $totalHours,
                'average_hours' => $averageHours,
            ],
            'daily_breakdown' => $dailyBreakdown,
        ];
    }
}
