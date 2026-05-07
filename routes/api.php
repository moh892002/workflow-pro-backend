<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::apiResource('users', UserController::class);

Route::apiResource('tasks', TaskController::class);

Route::apiResource('departments', DepartmentController::class);
