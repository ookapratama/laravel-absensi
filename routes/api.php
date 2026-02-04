<?php

use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
    
    // Test user route (removed original closure to use consistent controller pattern)
    // Route::get('/user', function (Request $request) {
    //    return $request->user();
    // });
});

// User API Routes
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserApiController::class, 'index']);
    Route::get('/{id}', [UserApiController::class, 'show']);
});
