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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        $this->validateEnvironment();
    }

    private function validateEnvironment(): void
    {
        if (! app()->environment('production')) {
            return;
        }

        $required = ['app.key'];
        foreach ($required as $key) {
            if (empty(config($key))) {
                throw new \RuntimeException('Environment variable APP_KEY is not set.');
            }
        }

        if (config('app.debug')) {
            throw new \RuntimeException('APP_DEBUG must be false in production.');
        }
    }
}
