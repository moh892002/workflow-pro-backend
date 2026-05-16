<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\TaskController;
// use App\Modules\Users\Controllers\UserController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function(){
//     Route::post('/logout', [AuthController::class, 'logout']);
// });


Route::apiResource('users', UserController::class);

Route::apiResource('tasks', TaskController::class);

Route::apiResource('departments', DepartmentController::class);

Route::apiResource('records', RecordController::class);
