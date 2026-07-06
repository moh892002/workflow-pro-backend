<?php

use App\Models\SalaryRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = makeUser(['role' => 'ADMIN']);
});

test('admin can list salary records', function () {
    $employee = makeUser();
    SalaryRecord::create([
        'user_id' => $employee->id,
        'amount' => 5000,
        'transaction_type' => 'salary',
        'transaction_date' => now(),
        'notes' => 'Monthly salary',
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson('/api/records');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'data']);
});

test('admin can create a salary record', function () {
    $employee = makeUser();

    $response = $this->actingAs($this->admin)
        ->postJson('/api/records', [
            'user_id' => $employee->id,
            'amount' => 7500,
            'transaction_type' => 'salary',
            'transaction_date' => now()->toDateString(),
            'notes' => 'August salary',
        ]);

    $response->assertStatus(201);

    expect(SalaryRecord::where('user_id', $employee->id)->exists())->toBeTrue();
});

test('admin can view a salary record', function () {
    $employee = makeUser();
    $record = SalaryRecord::create([
        'user_id' => $employee->id,
        'amount' => 5000,
        'transaction_type' => 'bonus',
        'transaction_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson("/api/records/{$record->id}");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('admin can delete a salary record', function () {
    $employee = makeUser();
    $record = SalaryRecord::create([
        'user_id' => $employee->id,
        'amount' => 5000,
        'transaction_type' => 'salary',
        'transaction_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->deleteJson("/api/records/{$record->id}");

    $response->assertStatus(200);
});

test('employee can view salary records', function () {
    $employee = makeUser(['role' => 'EMPLOYEE']);

    $response = $this->actingAs($employee)
        ->getJson('/api/records');

    $response->assertStatus(200);
});

test('admin can update a salary record', function () {
    $employee = makeUser();
    $record = SalaryRecord::create([
        'user_id' => $employee->id,
        'amount' => 5000,
        'transaction_type' => 'salary',
        'transaction_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->putJson("/api/records/{$record->id}", [
            'user_id' => $employee->id,
            'amount' => 6000,
            'transaction_type' => 'bonus',
            'transaction_date' => now()->toDateString(),
            'notes' => 'Updated bonus',
        ]);

    $response->assertStatus(200);
    expect((float) $record->fresh()->amount)->toEqual(6000.0);
    expect($record->fresh()->transaction_type)->toBe('bonus');
});

test('employee cannot update a salary record', function () {
    $employee = makeUser(['role' => 'EMPLOYEE']);
    $record = SalaryRecord::create([
        'user_id' => $employee->id,
        'amount' => 5000,
        'transaction_type' => 'salary',
        'transaction_date' => now(),
    ]);

    $response = $this->actingAs($employee)
        ->putJson("/api/records/{$record->id}", [
            'user_id' => $employee->id,
            'amount' => 6000,
            'transaction_type' => 'bonus',
            'transaction_date' => now()->toDateString(),
        ]);

    $response->assertStatus(403);
    expect((float) $record->fresh()->amount)->toEqual(5000.0);
});

test('update salary record requires validation', function () {
    $employee = makeUser();
    $record = SalaryRecord::create([
        'user_id' => $employee->id,
        'amount' => 5000,
        'transaction_type' => 'salary',
        'transaction_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->putJson("/api/records/{$record->id}", [
            'amount' => 'not-a-number',
        ]);

    $response->assertStatus(422);
});

test('create salary record requires validation', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/api/records', []);

    $response->assertStatus(422);
});
