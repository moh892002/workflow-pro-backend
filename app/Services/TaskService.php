<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    public function list(User $user, array $params = []): LengthAwarePaginator
    {
        $perPage = $params['per_page'] ?? 15;
        $query = Task::with('user');

        if ($user->role === 'EMPLOYEE') {
            $query->where('assigned_to', $user->id);
        }

        return $query->paginate($perPage, ['*'], 'page', $params['page'] ?? 1);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function find(int $id): ?Task
    {
        return Task::with('user')->find($id);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
