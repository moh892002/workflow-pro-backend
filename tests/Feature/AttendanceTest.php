<?php

use App\Models\AttendanceRecord;
use Carbon\Carbon;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->employee = makeUser(['role' => 'EMPLOYEE']);
});

test('user can check in', function () {
    $response = $this->actingAs($this->employee)
        ->postJson('/api/attendance/check-in');

    $response->assertStatus(201);

    expect(AttendanceRecord::where('user_id', $this->employee->id)
        ->where('date', now()->toDateString())
        ->exists())->toBeTrue();
});

test('user cannot check in twice same day', function () {
    $this->actingAs($this->employee)->postJson('/api/attendance/check-in');

    $response = $this->actingAs($this->employee)
        ->postJson('/api/attendance/check-in');

    $response->assertStatus(409);
});

test('user can check out after check in', function () {
    $record = AttendanceRecord::create([
        'user_id' => $this->employee->id,
        'date' => Carbon::today(),
        'check_in' => now()->subHours(8),
        'status' => 'PRESENT',
    ]);

    $response = $this->actingAs($this->employee)
        ->putJson("/api/attendance/{$record->id}/check-out");

    $response->assertStatus(200);

    expect(AttendanceRecord::find($record->id)->check_out)->not->toBeNull();
});

test('user can get today attendance', function () {
    AttendanceRecord::create([
        'user_id' => $this->employee->id,
        'date' => Carbon::today(),
        'check_in' => now()->subHours(4),
        'status' => 'PRESENT',
    ]);

    $response = $this->actingAs($this->employee)
        ->getJson('/api/attendance/today');

    $response->assertStatus(200);
});

test('user can get attendance history', function () {
    AttendanceRecord::create([
        'user_id' => $this->employee->id,
        'date' => Carbon::today()->subDay(),
        'check_in' => now()->subDay()->setHour(8),
        'check_out' => now()->subDay()->setHour(17),
        'status' => 'PRESENT',
    ]);

    $response = $this->actingAs($this->employee)
        ->getJson('/api/attendance/history');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'data']);
});

test('admin can list all attendance', function () {
    $admin = makeUser(['role' => 'ADMIN']);

    $response = $this->actingAs($admin)
        ->getJson('/api/attendance');

    $response->assertStatus(200);
});

test('user can auto check in when no record exists', function () {
    $response = $this->actingAs($this->employee)
        ->postJson('/api/attendance/auto-check-in');

    $response->assertStatus(201);

    expect(AttendanceRecord::where('user_id', $this->employee->id)
        ->where('date', now()->toDateString())
        ->exists())->toBeTrue();
});

test('auto check in returns existing record when already checked in', function () {
    $this->actingAs($this->employee)->postJson('/api/attendance/check-in');

    $response = $this->actingAs($this->employee)
        ->postJson('/api/attendance/auto-check-in');

    $response->assertStatus(201);
    expect(
        AttendanceRecord::where('user_id', $this->employee->id)
            ->where('date', now()->toDateString())
            ->count()
    )->toBe(1);
});

test('auto check out creates check out for existing record', function () {
    AttendanceRecord::create([
        'user_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'check_in' => now()->subHours(8),
        'status' => 'PRESENT',
    ]);

    $response = $this->actingAs($this->employee)
        ->postJson('/api/attendance/auto-check-out');

    $response->assertStatus(200);
    expect(AttendanceRecord::where('user_id', $this->employee->id)
        ->where('date', now()->toDateString())
        ->first()
        ->check_out
    )->not->toBeNull();
});

test('auto check out returns existing record when already checked out', function () {
    $record = AttendanceRecord::create([
        'user_id' => $this->employee->id,
        'date' => now()->toDateString(),
        'check_in' => now()->subHours(8),
        'check_out' => now()->subHours(1),
        'status' => 'PRESENT',
    ]);

    $response = $this->actingAs($this->employee)
        ->postJson('/api/attendance/auto-check-out');

    $response->assertStatus(200);
    expect(AttendanceRecord::find($record->id)->check_out)->not->toBeNull();
});

test('auto check out returns 404 when no check in record exists', function () {
    $response = $this->actingAs($this->employee)
        ->postJson('/api/attendance/auto-check-out');

    $response->assertStatus(404);
});

test('unauthenticated user cannot access attendance', function () {
    $response = $this->getJson('/api/attendance/today');
    $response->assertStatus(401);
});
