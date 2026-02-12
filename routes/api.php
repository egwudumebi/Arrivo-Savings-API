<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:auth-register');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:auth-login');

        Route::middleware(['auth:api', 'throttle:auth-refresh'])->group(function (): void {
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    Route::middleware(['auth:api'])->group(function (): void {
        Route::get('profile', function (Request $request) {
            return response()->json([
                'message' => 'Authenticated',
                'user_id' => $request->user('api')?->getKey(),
                'role' => $request->user('api')?->role,
            ]);
        });

        Route::get('admin/ping', function () {
            return response()->json(['message' => 'Admin OK']);
        })->middleware('admin');

        Route::get('super-admin/ping', function () {
            return response()->json(['message' => 'Super Admin OK']);
        })->middleware('super_admin');
    });
});
