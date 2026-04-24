<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many requests, please slow down',
                    ], 429);
                });
        });

        RateLimiter::for('leads', function (Request $request) {
            $perHour = (app()->environment('production') || app()->environment('testing')) ? 5 : 60;

            return Limit::perHour($perHour)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many applications. Please try again in an hour.',
                    ], 429);
                });
        });

        RateLimiter::for('leads-burst', function (Request $request) {
            $perMinute = (app()->environment('production') || app()->environment('testing')) ? 3 : 10;

            return Limit::perMinute($perMinute)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many requests, please slow down',
                    ], 429);
                });
        });

        RateLimiter::for('scholarship-applications', function (Request $request) {
            $perHour = (app()->environment('production') || app()->environment('testing')) ? 3 : 60;

            return Limit::perHour($perHour)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many applications. Please try again in an hour.',
                    ], 429);
                });
        });

        RateLimiter::for('scholarship-burst', function (Request $request) {
            $perMinute = (app()->environment('production') || app()->environment('testing')) ? 2 : 10;

            return Limit::perMinute($perMinute)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many requests, please slow down',
                    ], 429);
                });
        });

        RateLimiter::for('contact-messages', function (Request $request) {
            $perHour = (app()->environment('production') || app()->environment('testing')) ? 5 : 60;

            return Limit::perHour($perHour)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many messages. Please try again in an hour.',
                    ], 429);
                });
        });

        RateLimiter::for('contact-burst', function (Request $request) {
            $perMinute = (app()->environment('production') || app()->environment('testing')) ? 3 : 10;

            return Limit::perMinute($perMinute)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Too many requests, please slow down',
                    ], 429);
                });
        });
    }
}
