<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;
use App\Classes\ApiResponseClass;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function (Request $request) {

            $apiVersion = $request->header('X-Api-Version', env('X_API_VERSION'));

            $routeFile = $apiVersion != env('X_API_VERSION') ? base_path(sprintf('routes/%s.%s.php', 'api', $apiVersion)) : base_path(sprintf('routes/%s.php', 'api'));
            
            if (!file_exists($routeFile)) {
                return ApiResponseClass::badRequest('File Version Missing');
            }

            Route::middleware('api')
                    ->prefix('api')
                    ->group($routeFile);

        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->throttleWithRedis();

        $middleware->alias([
            'firebase.auth' => \App\Http\Middleware\FirebaseAuthMiddleware::class
        ]);
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponseClass::notFound($e);
            }
        });
    })->create();
