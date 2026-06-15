<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $tasks = Task::with('user')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => TaskResource::collection($tasks),
        ], 200);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $task,
        ], 201);
    }

    public function show($id)
    {
        $task = Task::with('user')->find($id);
        if (! $task) {
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

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::find($id);
        if (! $task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }

        $task->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $task,
        ], 200);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if (! $task) {
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
