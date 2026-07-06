<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDepartmentRequest;
use App\Http\Requests\Api\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    public function __construct(
        private readonly DepartmentService $departmentService,
    ) {}

    public function index(Request $request)
    {
        $departments = Cache::remember('departments:all', 3600, function () {
            return Department::all(['id', 'name', 'created_at', 'updated_at']);
        });

        return $this->success($departments);
    }

    public function store(StoreDepartmentRequest $request)
    {
        Cache::forget('departments:all');

        return $this->created($this->departmentService->create($request->validated()));
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        Cache::forget('departments:all');

        $this->authorize('update', $department);

        $this->departmentService->update($department, $request->validated());

        return $this->success($department);
    }

    public function show(Department $department)
    {
        $this->authorize('view', $department);

        return $this->success($department);
    }

    public function destroy(Department $department)
    {
        Cache::forget('departments:all');

        $this->authorize('delete', $department);

        $this->departmentService->delete($department);

        return $this->message('Department deleted successfully');
    }
}
