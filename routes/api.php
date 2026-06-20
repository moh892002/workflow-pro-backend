<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\PerformanceReviewController;
use App\Http\Controllers\Api\RecordController;
use App\Http\Controllers\Api\RecycleBinController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('department');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);

    Route::apiResource('performance-reviews', PerformanceReviewController::class);

    Route::apiResource('tasks', TaskController::class);

    Route::apiResource('departments', DepartmentController::class);

    Route::apiResource('records', RecordController::class);

    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
    Route::post('/activity-logs', [ActivityLogController::class, 'store']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:ADMIN,HR_MANAGER'])->prefix('reports')->group(function () {
    Route::get('/attendance', [ReportController::class, 'attendance']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::get('/attendance/today', [AttendanceController::class, 'today']);
    Route::get('/attendance/history', [AttendanceController::class, 'history']);
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::put('/attendance/{attendanceRecord}/check-out', [AttendanceController::class, 'checkOut']);
    Route::post('/attendance/auto-check-in', [AttendanceController::class, 'autoCheckIn']);
    Route::post('/attendance/auto-check-out', [AttendanceController::class, 'autoCheckOut']);
});

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

// Recycle Bin API Routes
Route::middleware(['auth:sanctum', 'role:ADMIN,HR_MANAGER'])->prefix('recycle-bin')->group(function () {
    Route::get('/', [RecycleBinController::class, 'index']);
    Route::get('/{model}', [RecycleBinController::class, 'showByModel']);
    Route::post('/{model}/{id}/restore', [RecycleBinController::class, 'restore']);
    Route::delete('/{model}/{id}/force', [RecycleBinController::class, 'forceDelete']);
    Route::post('/bulk-restore', [RecycleBinController::class, 'bulkRestore']);
    Route::delete('/bulk-force-delete', [RecycleBinController::class, 'bulkForceDelete']);
});
