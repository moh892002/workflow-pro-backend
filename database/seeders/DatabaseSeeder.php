<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\PerformanceReviewSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'fullname' => 'Admin User',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'ADMIN',
            'job_title' => 'System Administrator',
            'salary' => 0,
        ]);

        User::create([
            'fullname' => 'HR Manager',
            'email' => 'hr@example.com',
            'username' => 'hr',
            'password' => Hash::make('password'),
            'role' => 'HR_MANAGER',
            'job_title' => 'Human Resources Manager',
            'salary' => 0,
        ]);

        User::create([
            'fullname' => 'Employee User',
            'email' => 'employee@example.com',
            'username' => 'employee',
            'password' => Hash::make('password'),
            'role' => 'EMPLOYEE',
            'job_title' => 'Employee',
            'salary' => 0,
        ]);

        $this->call([
            PerformanceReviewSeeder::class,
        ]);
    }
}
