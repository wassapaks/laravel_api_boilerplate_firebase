<?php

namespace App\Providers;

use App\Classes\ApiResponseClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        RateLimiter::for('api', function (Request $request) {
            $limit = ($request->bearerToken()) ? 1000 : 50;
            return Limit::perMinute($limit)->by($request->ip())->response(function (Request $request, array $headers) {
                return ApiResponseClass::tooManyRequest($request->ip());
            });
        });
    }
}
