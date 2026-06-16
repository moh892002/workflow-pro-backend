<?php

namespace App\Services;

use App\Models\SalaryRecord;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class SalaryRecordService
{
    public function list(User $user, array $params = []): LengthAwarePaginator
    {
        $perPage = $params['per_page'] ?? 15;
        $query = SalaryRecord::with('user.department');

        if ($user->role === 'EMPLOYEE') {
            $query->where('user_id', $user->id);
        }

        return $query->paginate($perPage, ['*'], 'page', $params['page'] ?? 1);
    }

    public function create(array $data): SalaryRecord
    {
        return SalaryRecord::create($data);
    }

    public function find(int $id): ?SalaryRecord
    {
        return SalaryRecord::with('user')->find($id);
    }

    public function update(SalaryRecord $record, array $data): SalaryRecord
    {
        $record->update($data);
        return $record;
    }

    public function delete(SalaryRecord $record): void
    {
        $record->delete();
    }
}
