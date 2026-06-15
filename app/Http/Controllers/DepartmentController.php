<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreDepartmentRequest;
use App\Http\Requests\Api\UpdateDepartmentRequest;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        private readonly DepartmentService $departmentService,
    ) {}

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        return $this->success($this->departmentService->list($perPage, $page));
    }

    public function store(StoreDepartmentRequest $request)
    {
        return $this->created($this->departmentService->create($request->validated()));
    }

    public function show($id)
    {
        $department = $this->departmentService->find($id);

        if (! $department) {
            return $this->error('Department not found', 404);
        }

        return $this->success($department);
    }

    public function update(UpdateDepartmentRequest $request, $id)
    {
        $department = $this->departmentService->find($id);
        if (! $department) {
            return $this->error('Department not found', 404);
        }

        $this->departmentService->update($department, $request->validated());

        return $this->success($department);
    }

    public function destroy($id)
    {
        $department = $this->departmentService->find($id);

        if (! $department) {
            return $this->error('Department not found', 404);
        }

        $this->departmentService->delete($department);

        return $this->message('Department deleted successfully');
    }
}
