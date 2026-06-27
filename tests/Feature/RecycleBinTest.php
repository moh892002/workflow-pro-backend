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

test('admin can view recycle bin filtered by model', function () {
    $targetUser = makeUser();
    $this->actingAs($this->admin);
    $targetUser->delete();

    $response = $this->actingAs($this->admin)
        ->getJson('/api/recycle-bin/User');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'data']);
});

test('showByModel returns empty for non-existent model', function () {
    $response = $this->actingAs($this->admin)
        ->getJson('/api/recycle-bin/NonExistentModel');

    $response->assertStatus(200);
    expect($response->json('data.data'))->toBeArray();
});

test('admin can bulk restore deleted records', function () {
    $user1 = makeUser();
    $user2 = makeUser();
    $this->actingAs($this->admin);
    $user1->delete();
    $user2->delete();

    $bin1 = RecycleBin::where('deleted_item_id', $user1->id)->first();
    $bin2 = RecycleBin::where('deleted_item_id', $user2->id)->first();

    $response = $this->actingAs($this->admin)
        ->postJson('/api/recycle-bin/bulk-restore', [
            'records' => [
                ['model' => 'User', 'id' => $user1->id],
                ['model' => 'User', 'id' => $user2->id],
            ],
        ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(User::find($user1->id))->not->toBeNull();
    expect(User::find($user2->id))->not->toBeNull();
    expect(RecycleBin::find($bin1->id))->toBeNull();
    expect(RecycleBin::find($bin2->id))->toBeNull();
});

test('bulk restore validation fails without records', function () {
    $response = $this->actingAs($this->admin)
        ->postJson('/api/recycle-bin/bulk-restore', []);

    $response->assertStatus(400);
});

test('admin can bulk force delete records', function () {
    $targetUser = makeUser();
    $this->actingAs($this->admin);
    $targetUser->delete();

    $bin = RecycleBin::where('deleted_item_id', $targetUser->id)->first();

    $response = $this->actingAs($this->admin)
        ->deleteJson('/api/recycle-bin/bulk-force-delete', [
            'records' => [
                ['model' => 'User', 'id' => $targetUser->id],
            ],
        ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(User::withTrashed()->find($targetUser->id))->toBeNull();
    expect(RecycleBin::find($bin->id))->toBeNull();
});

test('bulk force delete validation fails without records', function () {
    $response = $this->actingAs($this->admin)
        ->deleteJson('/api/recycle-bin/bulk-force-delete', []);

    $response->assertStatus(400);
});

test('unauthenticated user cannot access recycle bin', function () {
    $response = $this->getJson('/api/recycle-bin');
    $response->assertStatus(401);
});
