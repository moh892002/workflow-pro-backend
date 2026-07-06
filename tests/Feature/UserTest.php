<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = makeUser(['role' => 'ADMIN']);
});

test('admin can list users', function () {
    makeUser();

    $response = $this->actingAs($this->admin)
        ->getJson('/api/users');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'data']);
});

test('employee cannot list users', function () {
    $employee = makeUser(['role' => 'EMPLOYEE']);

    $response = $this->actingAs($employee)
        ->getJson('/api/users');

    $response->assertStatus(403);
});

test('admin can create a user', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/api/users', [
            'fullname' => 'New User',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'role' => 'EMPLOYEE',
            'job_title' => 'Developer',
            'salary' => 50000,
            'password' => 'password123',
        ]);

    $response->assertStatus(201)
        ->assertJson(['success' => true]);

    expect(User::where('email', 'new@example.com')->exists())->toBeTrue();
});

test('admin can view a user', function () {
    $target = makeUser();

    $response = $this->actingAs($this->admin)
        ->getJson("/api/users/{$target->id}");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('admin can update a user', function () {
    $target = makeUser();

    $response = $this->actingAs($this->admin)
        ->putJson("/api/users/{$target->id}", [
            'fullname' => 'Updated Name',
            'email' => $target->email,
            'username' => $target->username,
            'role' => 'EMPLOYEE',
        ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(User::find($target->id)->fullname)->toBe('Updated Name');
});

test('admin can delete a user', function () {
    $target = makeUser();

    $response = $this->actingAs($this->admin)
        ->deleteJson("/api/users/{$target->id}");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('unauthenticated user cannot access users', function () {
    $response = $this->getJson('/api/users');
    $response->assertStatus(401);
});

test('create user requires validation', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/api/users', []);

    $response->assertStatus(422);
});
