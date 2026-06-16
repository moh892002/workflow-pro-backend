<?php

use App\Models\Task;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->employee = makeUser(['role' => 'EMPLOYEE']);
});

test('user can list tasks', function () {
    Task::create([
        'assigned_to' => $this->employee->id,
        'title' => 'Test Task',
        'description' => 'A task description',
        'priority' => 'MEDIUM',
        'status' => 'pending',
        'deadline_date' => now()->addDay(),
    ]);

    $response = $this->actingAs($this->employee)
        ->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'data']);
});

test('user can create a task', function () {
    $response = $this->actingAs($this->employee)
        ->postJson('/api/tasks', [
            'title' => 'New Task',
            'description' => 'Task description',
            'priority' => 'HIGH',
            'status' => 'pending',
            'deadline_date' => now()->addWeek()->toDateString(),
        ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);

    expect(Task::where('title', 'New Task')->exists())->toBeTrue();
});

test('user can view a task', function () {
    $task = Task::create([
        'assigned_to' => $this->employee->id,
        'title' => 'View Task',
        'description' => 'View task description',
        'priority' => 'LOW',
        'status' => 'pending',
        'deadline_date' => now()->addDay(),
    ]);

    $response = $this->actingAs($this->employee)
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('user can update their task', function () {
    $task = Task::create([
        'assigned_to' => $this->employee->id,
        'title' => 'Original',
        'description' => 'Original description',
        'priority' => 'LOW',
        'status' => 'pending',
        'deadline_date' => now()->addDay(),
    ]);

    $response = $this->actingAs($this->employee)
        ->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated',
            'description' => 'Updated description',
            'priority' => 'HIGH',
            'status' => 'in_progress',
            'deadline_date' => now()->addDay()->toDateString(),
        ]);

    $response->assertStatus(200);

    expect(Task::find($task->id)->title)->toBe('Updated');
});

test('user can delete a task', function () {
    $task = Task::create([
        'assigned_to' => $this->employee->id,
        'title' => 'Delete Me',
        'description' => 'Task to delete',
        'priority' => 'LOW',
        'status' => 'pending',
        'deadline_date' => now()->addDay(),
    ]);

    $response = $this->actingAs($this->employee)
        ->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(200);
});

test('create task requires validation', function () {
    $response = $this->actingAs($this->employee)
        ->postJson('/api/tasks', []);

    $response->assertStatus(422);
});

test('unauthenticated user cannot access tasks', function () {
    $response = $this->getJson('/api/tasks');
    $response->assertStatus(401);
});
