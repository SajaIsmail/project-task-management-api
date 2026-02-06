<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});

// Protected routes
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {


    // User profile
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    // Task routes (CRUD)
    Route::get('/tasks', [TaskController::class, 'index']);        // Get all tasks
    Route::post('/tasks', [TaskController::class, 'store']);       // Create task
    Route::get('/tasks/{task}', [TaskController::class, 'show']);  // Get single task
    Route::put('/tasks/{task}', [TaskController::class, 'update']); // Update task
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']); // Delete task
    Route::post('/task-dependency', [TaskController::class, 'addDependency']);
    Route::post('/tasks/{task}/dependencies', [TaskController::class, 'addDependency']);
    Route::post('/tasks/{task}/complete', [TaskController::class, 'completeTask']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
