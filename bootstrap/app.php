<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            // Always JSON for API routes
            if ($request->is('api/*')) {
                return true;
            }

            // Always JSON for broadcasting auth
            if ($request->is('broadcasting/*')) {
                return true;
            }

            // Otherwise default behavior
            return $request->expectsJson();
        });
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->is('broadcasting/*')) {
                return response()->json([
                    'success' => false,
                    'status' => 401,
                    'errors' => $e->getMessage(),
                    'from' => 'app default response',
                    'message' => "UnAunthenticated",
                ], 401);
            }
        });
    })->create();
