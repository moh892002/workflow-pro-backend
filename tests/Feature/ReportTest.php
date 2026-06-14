<?php

use App\Models\User;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeUser(array $overrides = []): User
{
    $attrs = array_merge([
        'fullname' => 'Test User',
        'username' => 'test_' . uniqid(),
        'email' => uniqid() . '@example.com',
        'role' => 'EMPLOYEE',
        'job_title' => 'Tester',
        'salary' => 0,
        'password' => Hash::make('password'),
    ], $overrides);

    return User::create($attrs);
}

test('admin can generate attendance report', function () {
    $admin = makeUser(['role' => 'ADMIN', 'fullname' => 'Admin']);
    $employee = makeUser(['fullname' => 'Employee']);

    $today = Carbon::today();
    AttendanceRecord::create([
        'user_id' => $employee->id,
        'date' => $today,
        'check_in' => $today->copy()->setTime(8, 0),
        'check_out' => $today->copy()->setTime(17, 0),
        'status' => 'PRESENT',
    ]);

    $response = $this->actingAs($admin)->getJson('/api/reports/attendance?' . http_build_query([
        'user_id' => $employee->id,
        'start_date' => $today->copy()->startOfMonth()->toDateString(),
        'end_date' => $today->copy()->endOfMonth()->toDateString(),
    ]));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => ['employee', 'period', 'stats', 'daily_breakdown'],
        ]);

    expect($response->json('success'))->toBeTrue();
});

test('hr manager cannot generate report for self', function () {
    $hr = makeUser(['role' => 'HR_MANAGER']);

    $response = $this->actingAs($hr)->getJson('/api/reports/attendance?' . http_build_query([
        'user_id' => $hr->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
    ]));

    $response->assertStatus(403);
});

test('unauthorized users cannot access reports', function () {
    $employee = makeUser();
    $target = makeUser();

    $response = $this->actingAs($employee)->getJson('/api/reports/attendance?' . http_build_query([
        'user_id' => $target->id,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
    ]));

    $response->assertStatus(403);
});
