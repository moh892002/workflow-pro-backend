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

        return $this->success(TaskResource::collection($tasks));
    }

    public function store(StoreTaskRequest $request)
    {
        return $this->created($this->taskService->create($request->validated()));
    }

    public function show($id)
    {
        $task = $this->taskService->find($id);
        if (! $task) {
            return $this->error('Task not found', 404);
        }

        return $this->success($task);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = $this->taskService->find($id);
        if (! $task) {
            return $this->error('Task not found', 404);
        }

        $this->taskService->update($task, $request->validated());

        return $this->success($task);
    }

    public function destroy($id)
    {
        $task = $this->taskService->find($id);
        if (! $task) {
            return $this->error('Task not found', 404);
        }

        $this->taskService->delete($task);

        return $this->message('Task deleted successfully');
    }
}
