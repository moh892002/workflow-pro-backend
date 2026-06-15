<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class DepartmentService
{
    public function list(int $perPage = 15, int $page = 1): array
    {
        $cacheKey = "departments:list:page={$page}:per_page={$perPage}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($perPage, $page) {
            return Department::paginate($perPage, ['*'], 'page', $page)->toArray();
        });
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function find(int $id): ?Department
    {
        return Department::find($id);
    }

    public function update(Department $department, array $data): Department
    {
        $department->update($data);
        return $department;
    }

    public function delete(Department $department): void
    {
        $department->delete();
    }
}
