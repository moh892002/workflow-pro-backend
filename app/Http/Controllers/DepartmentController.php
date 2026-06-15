<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreDepartmentRequest;
use App\Http\Requests\Api\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
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

        return response()->json([
            'success' => true,
            'data' => $this->departmentService->list($perPage, $page),
        ], 200);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = $this->departmentService->create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $department,
        ], 201);
    }

    public function show($id)
    {
        $department = $this->departmentService->find($id);

        if (! $department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $department,
        ], 200);
    }

    public function update(UpdateDepartmentRequest $request, $id)
    {
        $department = $this->departmentService->find($id);
        if (! $department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $this->departmentService->update($department, $request->validated());

        return response()->json([
            'success' => true,
            'data' => $department,
        ], 200);
    }

    public function destroy($id)
    {
        $department = $this->departmentService->find($id);

        if (! $department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $this->departmentService->delete($department);

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully',
        ], 200);
    }
}
