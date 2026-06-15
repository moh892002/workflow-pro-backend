<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\PerformanceReview;
use App\Models\SalaryRecord;
use App\Models\Task;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use App\Policies\AttendanceRecordPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\PerformanceReviewPolicy;
use App\Policies\SalaryRecordPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(SalaryRecord::class, SalaryRecordPolicy::class);
        Gate::policy(AttendanceRecord::class, AttendanceRecordPolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
        Gate::policy(PerformanceReview::class, PerformanceReviewPolicy::class);
    }
}
