<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = makeUser([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
});

test('user can login with valid credentials', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token', 'user']);
});

test('user cannot login with invalid credentials', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
});

test('authenticated user can retrieve their profile', function () {
    $response = $this->actingAs($this->user)
        ->getJson('/api/user');

    $response->assertStatus(200)
        ->assertJson([
            'id' => $this->user->id,
            'email' => 'test@example.com',
        ]);
});

test('unauthenticated user cannot access profile', function () {
    $response = $this->getJson('/api/user');

    $response->assertStatus(401);
});

test('user can logout', function () {
    $token = $this->user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/logout');

    $response->assertStatus(200);

    expect($this->user->tokens()->count())->toBe(0);
});
