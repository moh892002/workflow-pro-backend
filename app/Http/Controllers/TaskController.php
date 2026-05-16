<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(){
        $tasks = Task::with(['user'])->get();
        return response()->json([
            'success' => true,
            'data' => $tasks,
        ], 200);
    }

    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'status' => 'required|in:completed,pending,in_progress',
            'deadline_date' => 'required|date_format:Y-m-d',
            'assigned_to' => 'nullable|exists:users,id',
            // 'project_id' => 'nullable|exists:projects,id',
        ]);

        $task = Task::create($validated);
        return response()->json([
            'success' => true,
            'data' => $task,
        ], 201);
    }

    public function show($id){
        $task = Task::with(['user'])->find($id);
        if(!$task){
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $task,
        ], 200);
    }

    public function update(Request $request, $id){
        $task = Task::find($id);
        if(!$task){
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'status' => 'required|in:completed,pending,in_progress',
            'deadline_date' => 'required|date_format:Y-m-d',
            'assigned_to' => 'nullable|exists:users,id',
            // 'project_id' => 'nullable|exists:projects,id',
        ]);

        $task->update($validated);
        return response()->json([
            'success' => true,
            'data' => $task,
        ], 200);
    }

    public function destroy($id){
        $task = Task::find($id);
        if(!$task){
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }
        $task->delete();
        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully',
        ], 200);
    }
}
