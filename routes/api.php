<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AutoReplyRuleController;


Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware([
            'auth:api',
            'throttle:10,1',
        ]);
});




Route::middleware(['auth:api', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get(
            'auto-replies/fallback',
            [AutoReplyRuleController::class, 'fallback']
        )->middleware('throttle:10,1');

        Route::put(
            'auto-replies/fallback',
            [AutoReplyRuleController::class, 'updateFallback']
        )->middleware('throttle:10,1');

        Route::apiResource(
            'auto-replies',
            AutoReplyRuleController::class
        );

        Route::patch(
            'auto-replies/{id}/toggle',
            [AutoReplyRuleController::class, 'toggle']
        )->middleware('throttle:10,1');
    });




Route::middleware('auth:api')->group(function () {

    Route::post('/conversations', [ConversationController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::get('/conversations', [ConversationController::class, 'index'])
        ->middleware('throttle:10,1');

    Route::get('/conversations/{id}', [ConversationController::class, 'show'])
        ->middleware('throttle:10,1');

    Route::patch('/conversations/{id}/close', [ConversationController::class, 'close'])
        ->middleware('throttle:10,1');

    Route::get('/conversations/{id}/messages', [MessageController::class, 'index'])
        ->middleware('throttle:10,1');

    Route::post(
        '/conversations/{id}/messages',
        [MessageController::class, 'store']
    )->middleware('throttle:10,1');
});