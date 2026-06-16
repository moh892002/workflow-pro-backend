<?php

use App\Models\Department;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->admin = makeUser(['role' => 'ADMIN']);
});

test('admin can list departments', function () {
    Department::create(['name' => 'Engineering', 'description' => 'Build things']);

    $response = $this->actingAs($this->admin)
        ->getJson('/api/departments');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'data']);
});

test('admin can create a department', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/api/departments', [
            'name' => 'Engineering',
            'description' => 'Builds software',
        ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);

    expect(Department::where('name', 'Engineering')->exists())->toBeTrue();
});

test('admin can view a department', function () {
    $department = Department::create(['name' => 'HR', 'description' => 'People team']);

    $response = $this->actingAs($this->admin)
        ->getJson("/api/departments/{$department->id}");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('admin can update a department', function () {
    $department = Department::create(['name' => 'Old Name', 'description' => 'Old desc']);

    $response = $this->actingAs($this->admin)
        ->putJson("/api/departments/{$department->id}", [
            'name' => 'New Name',
            'description' => 'New desc',
        ]);

    $response->assertStatus(200);

    expect(Department::find($department->id)->name)->toBe('New Name');
});

test('admin can delete a department', function () {
    $department = Department::create(['name' => 'Temp', 'description' => 'Temp dept']);

    $response = $this->actingAs($this->admin)
        ->deleteJson("/api/departments/{$department->id}");

    $response->assertStatus(200);
});

test('employee can list departments', function () {
    $employee = makeUser(['role' => 'EMPLOYEE']);

    $response = $this->actingAs($employee)
        ->getJson('/api/departments');

    $response->assertStatus(200);
});

test('create department requires a name', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/api/departments', []);

    $response->assertStatus(422);
});
