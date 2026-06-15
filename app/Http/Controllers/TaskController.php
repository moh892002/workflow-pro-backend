<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    public function index(Request $request)
    {
        $tasks = $this->taskService->list($request->only(['per_page', 'page']));

        return response()->json([
            'success' => true,
            'data' => TaskResource::collection($tasks),
        ], 200);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $task,
        ], 201);
    }

    public function show($id)
    {
        $task = $this->taskService->find($id);
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
        $task = $this->taskService->find($id);
        if (! $task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }

        $this->taskService->update($task, $request->validated());

        return response()->json([
            'success' => true,
            'data' => $task,
        ], 200);
    }

    public function destroy($id)
    {
        $task = $this->taskService->find($id);
        if (! $task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found',
            ], 404);
        }

        $this->taskService->delete($task);

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully',
        ], 200);
    }
}
