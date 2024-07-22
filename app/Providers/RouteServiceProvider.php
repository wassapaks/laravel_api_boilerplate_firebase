<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Classes\ApiResponseClass;
use Illuminate\Http\Request;
class RouteServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $limit = ($request->bearerToken()) ? 1000 : 50;
            return Limit::perMinute($limit)->by($request->ip())->response(function (Request $request, array $headers) {
                return ApiResponseClass::tooManyRequest($request->ip());
            });
        });
    }

}
