<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Api\PerformanceReviewController;
use App\Http\Controllers\Api\RecycleBinController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/login', [AuthController::class, 'login']);

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

Route::middleware('auth:sanctum')->prefix('reports')->group(function () {
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

Route::post('/login', [AuthController::class, 'login']);

// Recycle Bin API Routes
Route::middleware('auth:sanctum')->prefix('recycle-bin')->group(function () {
    Route::get('/', [RecycleBinController::class, 'index']);
    Route::get('/{model}', [RecycleBinController::class, 'showByModel']);
    Route::post('/{model}/{id}/restore', [RecycleBinController::class, 'restore']);
    Route::delete('/{model}/{id}/force', [RecycleBinController::class, 'forceDelete']);
    Route::post('/bulk-restore', [RecycleBinController::class, 'bulkRestore']);
    Route::delete('/bulk-force-delete', [RecycleBinController::class, 'bulkForceDelete']);
});
