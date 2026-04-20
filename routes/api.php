<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\CohortController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\MentorController;
use App\Http\Controllers\Api\ScholarshipApplicationController;
use App\Http\Controllers\Api\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::post('auth/register', RegisterController::class);
    Route::post('auth/login', LoginController::class);

    // Public catalogue
    // IMPORTANT: courses/featured must be registered before courses/{slug}
    Route::get('courses/featured', [CourseController::class, 'featured']);
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{slug}', [CourseController::class, 'show']);

    Route::get('testimonials', [TestimonialController::class, 'index']);
    Route::get('mentors', [MentorController::class, 'index']);

    // Public lead capture (apply form + contact form)
    Route::post('leads', [LeadController::class, 'store'])->middleware(['throttle:leads', 'throttle:leads-burst']);

    // Public scholarship application
    Route::post('scholarship-applications', [ScholarshipApplicationController::class, 'store'])
        ->middleware(['throttle:scholarship-applications', 'throttle:scholarship-burst']);

    // Public cohort retrieval
    Route::get('cohorts', [CohortController::class, 'index']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', MeController::class);
        Route::post('auth/logout', LogoutController::class);
    });
});
