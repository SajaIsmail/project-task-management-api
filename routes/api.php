<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});

// Registration route
Route::post('/register', [AuthController::class, 'register']);

