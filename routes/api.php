<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

use App\Http\Controllers\Auth\AuthorizationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\FunctionBuildingController as AdminFunctionBuildingController;
use App\Http\Controllers\Admin\TypeDenunciationController as AdminTypeDenunciationController;
use App\Http\Controllers\Admin\BuildingController as AdminBuildingController;
use App\Http\Controllers\Admin\DenunciationController as AdminDenunciationController;
use App\Http\Controllers\Pelapor\DenunciationController as PelaporDenunciationController;

Route::middleware([
    EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
])->group(function () {
    Route::post('login', [AuthorizationController::class, 'login']);
    Route::post('logout', [AuthorizationController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthorizationController::class, 'me'])->middleware('auth:sanctum');
    Route::post('change_profile', [AuthorizationController::class, 'change_profile'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function() {
        Route::prefix('/admin')->group(function () {
            Route::resource('function_buildings', AdminFunctionBuildingController::class)->only([
                'index', 'store', 'update', 'destroy', 'show'
            ]);

            Route::resource('type_denunciations', AdminTypeDenunciationController::class)->only([
                'index', 'store', 'update', 'destroy', 'show'
            ]);

            Route::resource('buildings', AdminBuildingController::class)->only([
                'index', 'store', 'update', 'destroy', 'show'
            ]);

            Route::resource('users', AdminUserController::class)->only([
                'index', 'store', 'update', 'destroy', 'show'
            ]);

            Route::resource('denunciations', AdminDenunciationController::class)->only([
                'index', 'update', 'show'
            ]);
        });

        Route::prefix('/pelapor')->group(function () {
            Route::resource('denunciations', PelaporDenunciationController::class)->only([
                'index', 'store', 'update', 'show'
            ]);
        });
    });
});


