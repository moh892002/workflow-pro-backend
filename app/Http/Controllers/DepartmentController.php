<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreDepartmentRequest;
use App\Http\Requests\Api\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);
        $cacheKey = "departments:list:page={$page}:per_page={$perPage}";

        $departments = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($perPage, $page) {
            return DepartmentResource::collection(
                Department::paginate($perPage, ['*'], 'page', $page)
            )->resolve();
        });

        return response()->json([
            'success' => true,
            'data' => $departments,
        ], 200);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $department,
        ], 201);
    }

    public function show($id)
    {
        $department = Department::find($id);

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
        $department = Department::find($id);
        if (! $department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $department->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $department,
        ], 200);
    }

    public function destroy($id)
    {
        $department = Department::find($id);

        if (! $department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully',
        ], 200);
    }
}
