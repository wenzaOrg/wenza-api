<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\CourseController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::post('auth/register', RegisterController::class);
    Route::post('auth/login', LoginController::class);

    // Public catalog
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{slug}', [CourseController::class, 'show']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', MeController::class);
        Route::post('auth/logout', LogoutController::class);
    });
});
