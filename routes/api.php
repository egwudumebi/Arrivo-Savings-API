<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\SuperAdminController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Friends\FriendsController;
use App\Http\Controllers\Api\V1\Invitations\InvitationsController;
use App\Http\Controllers\Api\V1\Savings\GroupSavingsController;
use App\Http\Controllers\Api\V1\Savings\PersonalSavingsController;
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
        Route::prefix('friends')->group(function (): void {
            Route::get('/', [FriendsController::class, 'index']);
            Route::post('requests', [FriendsController::class, 'sendRequest']);
            Route::post('requests/{friendRequest}/accept', [FriendsController::class, 'acceptRequest']);
            Route::delete('{friend}', [FriendsController::class, 'remove']);
        });

        Route::prefix('personal-savings')->group(function (): void {
            Route::get('/', [PersonalSavingsController::class, 'index']);
            Route::post('/', [PersonalSavingsController::class, 'store']);
            Route::get('{personalSaving}', [PersonalSavingsController::class, 'show']);
            Route::put('{personalSaving}', [PersonalSavingsController::class, 'update']);
            Route::delete('{personalSaving}', [PersonalSavingsController::class, 'destroy']);
        });

        Route::prefix('group-savings')->group(function (): void {
            Route::get('/', [GroupSavingsController::class, 'index']);
            Route::post('/', [GroupSavingsController::class, 'store']);
            Route::get('{groupSaving}', [GroupSavingsController::class, 'show']);
            Route::put('{groupSaving}', [GroupSavingsController::class, 'update']);
            Route::delete('{groupSaving}', [GroupSavingsController::class, 'destroy']);

            Route::post('{groupSaving}/invite', [GroupSavingsController::class, 'invite']);
            Route::get('{groupSaving}/members', [GroupSavingsController::class, 'members']);
        });

        Route::prefix('invitations')->group(function (): void {
            Route::get('/', [InvitationsController::class, 'index']);
            Route::post('{invitation}/accept', [InvitationsController::class, 'accept']);
            Route::post('{invitation}/reject', [InvitationsController::class, 'reject']);
        });

        Route::get('profile', function (Request $request) {
            return response()->json([
                'message' => 'Authenticated',
                'user_id' => $request->user('api')?->getKey(),
                'role' => $request->user('api')?->role,
            ]);
        });

        Route::prefix('admin')->middleware('admin')->group(function (): void {
            Route::get('users', [AdminController::class, 'users']);
            Route::patch('users/{user}/suspend', [AdminController::class, 'suspend']);
            Route::get('savings', [AdminController::class, 'savings']);
        });

        Route::prefix('super-admin')->middleware('super_admin')->group(function (): void {
            Route::patch('users/{user}/promote-admin', [SuperAdminController::class, 'promote']);
            Route::get('stats', [SuperAdminController::class, 'stats']);
        });

        Route::get('admin/ping', function () {
            return response()->json(['message' => 'Admin OK']);
        })->middleware('admin');

        Route::get('super-admin/ping', function () {
            return response()->json(['message' => 'Super Admin OK']);
        })->middleware('super_admin');
    });
});
