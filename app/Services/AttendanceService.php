<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceService
{
    public function today(User $user): ?AttendanceRecord
    {
        return AttendanceRecord::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->first();
    }

    public function checkIn(User $user): AttendanceRecord
    {
        $existing = $this->today($user);

        if ($existing) {
            abort(409, 'Already checked in today');
        }

        $isLate = now()->hour > 9 || (now()->hour === 9 && now()->minute > 0);

        return AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'check_in' => now(),
            'status' => $isLate ? 'LATE' : 'PRESENT',
        ]);
    }

    public function checkOut(User $user, AttendanceRecord $record): AttendanceRecord
    {
        if ($record->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($record->check_out) {
            abort(409, 'Already checked out');
        }

        $record->update(['check_out' => now()]);

        return $record;
    }

    public function autoCheckIn(User $user): AttendanceRecord
    {
        $existing = $this->today($user);

        if ($existing) {
            return $existing;
        }

        $isLate = now()->hour > 9 || (now()->hour === 9 && now()->minute > 0);

        return AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'check_in' => now(),
            'status' => $isLate ? 'LATE' : 'PRESENT',
        ]);
    }

    public function autoCheckOut(User $user): AttendanceRecord
    {
        $record = $this->today($user);

        if (! $record) {
            abort(404, 'No check-in record found for today');
        }

        if ($record->check_out) {
            return $record;
        }

        $record->update(['check_out' => now()]);

        return $record;
    }

    public function history(User $user, int $perPage = 30): LengthAwarePaginator
    {
        return AttendanceRecord::where('user_id', $user->id)
            ->latest('date')
            ->paginate($perPage);
    }
}
