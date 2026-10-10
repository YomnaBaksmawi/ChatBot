<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\ConversationClosedException;
use App\Exceptions\AutoReplyRuleNotFoundException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
    __DIR__.'/../routes/channels.php',
    [
        'middleware' => ['api', 'auth:api'],
    ]
)
    ->withMiddleware(function (Middleware $middleware): void {
 $middleware->alias([
            'admin' => App\Http\Middleware\AdminMiddleware::class,
        ]);  
         $middleware->append(
        \App\Http\Middleware\SecurityHeaders::class
    );
      })
    ->withExceptions(function (Exceptions $exceptions): void {
         $exceptions->render(function (
        AuthenticationException $e,
        Request $request
    ) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => null,
            ], 401);
        }
    
    });
    $exceptions->render(function ( ValidationException $e, Request $request
    ) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }
    });

    $exceptions->render(function (
        NotFoundHttpException $e,
        Request $request
    ) {
        if ($request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'The requested resource was not found or is not accessible.',
                'errors' => null,
            ], 404);
        }
    });
    $exceptions->render(function (
    TokenInvalidException $e,
    Request $request
) {
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid token.',
            'errors' => null,
        ], 401);
    }
    });
    $exceptions->render(function (
    TokenExpiredException $e,
    Request $request
) {
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => 'Token has expired.',
            'errors' => null,
        ], 401);
    }
});
$exceptions->render(function (
    AuthorizationException $e,
    Request $request
) {
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => 'Forbidden.',
            'errors' => null,
        ], 403);
    }
});
$exceptions->render(function (
    ConversationClosedException $e,
    Request $request
) {
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'errors' => null,
        ], 422);
    }
});
$exceptions->render(function (
    AutoReplyRuleNotFoundException $e,
    Request $request
) {
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'errors' => null,
        ], 500);
    }
});
$exceptions->render(function (
    TooManyRequestsHttpException $e,
    Request $request
) {
    if ($request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => 'Too many requests.',
            'errors' => null,
        ], 429);
    }
});
    })->create();
