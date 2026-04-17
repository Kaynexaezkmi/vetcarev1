<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\EnsureUserIsVerified;
use App\Http\Middleware\RequireBearerApiToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => Admin::class,
            'api.bearer' => RequireBearerApiToken::class,
            'user.verified' => EnsureUserIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Not found.',
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });
    })->create();
