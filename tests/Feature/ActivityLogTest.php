<?php

use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->employee = makeUser(['role' => 'EMPLOYEE']);
});

test('user can log an activity', function () {
    $response = $this->actingAs($this->employee)
        ->postJson('/api/activity-logs', [
            'action' => 'LOGIN',
            'details' => 'User logged in',
        ]);

    $response->assertStatus(201);

    expect(ActivityLog::where('user_id', $this->employee->id)->exists())->toBeTrue();
});

test('user can list activity logs', function () {
    ActivityLog::create([
        'user_id' => $this->employee->id,
        'action' => 'LOGIN',
        'details' => 'Test log',
    ]);

    $response = $this->actingAs($this->employee)
        ->getJson('/api/activity-logs');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'data']);
});

test('admin can see all activity logs', function () {
    $admin = makeUser(['role' => 'ADMIN']);
    $other = makeUser();

    ActivityLog::create(['user_id' => $other->id, 'action' => 'LOGIN', 'details' => 'Other']);
    ActivityLog::create(['user_id' => $admin->id, 'action' => 'LOGIN', 'details' => 'Admin']);

    $response = $this->actingAs($admin)
        ->getJson('/api/activity-logs');

    $response->assertStatus(200);

    expect(count($response->json('data')))->toBeGreaterThanOrEqual(2);
});

test('unauthenticated user cannot access activity logs', function () {
    $response = $this->getJson('/api/activity-logs');
    $response->assertStatus(401);
});
