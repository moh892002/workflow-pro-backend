<?php

use App\Models\RecycleBin;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->admin = makeUser(['role' => 'ADMIN']);
});

test('admin can list recycle bin', function () {
    $target = makeUser();
    $this->actingAs($this->admin);
    $target->delete();

    $response = $this->actingAs($this->admin)
        ->getJson('/api/recycle-bin');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'data']);
});

test('admin can restore a soft-deleted user', function () {
    $target = makeUser();
    $this->actingAs($this->admin);
    $target->delete();

    $response = $this->actingAs($this->admin)
        ->postJson("/api/recycle-bin/User/{$target->id}/restore");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(User::find($target->id))->not->toBeNull();
});

test('admin can force delete a recycle bin entry', function () {
    $target = makeUser();
    $this->actingAs($this->admin);
    $target->delete();

    $response = $this->actingAs($this->admin)
        ->deleteJson("/api/recycle-bin/User/{$target->id}/force");

    $response->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('employee cannot access recycle bin', function () {
    $employee = makeUser(['role' => 'EMPLOYEE']);

    $response = $this->actingAs($employee)
        ->getJson('/api/recycle-bin');

    $response->assertStatus(403);
});

test('unauthenticated user cannot access recycle bin', function () {
    $response = $this->getJson('/api/recycle-bin');
    $response->assertStatus(401);
});
