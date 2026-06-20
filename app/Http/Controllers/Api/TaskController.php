<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    public function index(Request $request)
    {
        $tasks = $this->taskService->list($request->user(), $request->only(['per_page', 'page']));

        return $this->success(TaskResource::collection($tasks));
    }

    public function store(StoreTaskRequest $request)
    {
        if ($request->user()->role === 'EMPLOYEE' && $request->filled('assigned_to') && $request->input('assigned_to') !== $request->user()->id) {
            return $this->error('Employees may only assign tasks to themselves', 403);
        }

        $data = $request->validated();
        if (! $request->filled('assigned_to')) {
            $data['assigned_to'] = $request->user()->id;
        }

        return $this->created($this->taskService->create($data));
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return $this->success($task->load('user'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        if ($request->user()->role === 'EMPLOYEE' && $request->filled('assigned_to') && $request->input('assigned_to') !== $request->user()->id) {
            return $this->error('Employees may only assign tasks to themselves', 403);
        }

        $data = $request->validated();
        if (! $request->filled('assigned_to')) {
            $data['assigned_to'] = $task->assigned_to;
        }

        $this->taskService->update($task, $data);

        return $this->success($task);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $this->taskService->delete($task);

        return $this->message('Task deleted successfully');
    }
}
