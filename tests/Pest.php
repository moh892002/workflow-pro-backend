<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature');

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
