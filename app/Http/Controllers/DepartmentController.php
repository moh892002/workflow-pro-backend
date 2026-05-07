<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(){
        $departments = Department::get('name');
//        dd($departments);
        return response()->json([
            'success' => true,
            'data' => $departments,
        ], 200);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|unique:departments|string',
        ]);

        $department = Department::create($validated);
        return response()->json([
            'success' => true,
            'data' => $department,
        ], 201);
    }

    public function show($id){
        $department = Department::find($id);

        if(!$department){
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

    public function update(Request $request, $id) {
        $department = Department::find($id);
        if(!$department){
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|unique:departments|string',
        ]);

        $department->update($validated);

        return response()->json([
            'success' => true,
            'data' => $department,
        ], 200);
    }

    public function destroy($id){
        $department = Department::find($id);

        if(!$department) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found',
            ], 404);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully'
        ], 200);
    }
}
