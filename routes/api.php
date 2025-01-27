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
use App\Http\Controllers\Pelapor\FunctionBuildingController as PelaporFunctionBuildingController;
use App\Http\Controllers\Pelapor\TypeDenunciationController as PelaporTypeDenunciationController;
use App\Http\Controllers\Petugas\BuildingController as PetugasBuildingController;
use App\Http\Controllers\Admin\FunctionBuildingController as PetugasFunctionBuildingController;

Route::middleware([
    EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
])->group(function () {
    Route::post('login', [AuthorizationController::class, 'login']);
    Route::post('logout', [AuthorizationController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me', [AuthorizationController::class, 'me'])->middleware('auth:sanctum');
    Route::post('change_profile', [AuthorizationController::class, 'change_profile'])->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        // Admin
        Route::middleware('admin')->group(function () {
            Route::prefix('/admin')->group(function () {
                Route::resource('function_buildings', AdminFunctionBuildingController::class)->only([
                    'index', 'store', 'update', 'destroy', 'show'
                ]);

                Route::resource('type_denunciations', AdminTypeDenunciationController::class)->only([
                    'index', 'store', 'update', 'destroy', 'show'
                ]);

                Route::resource('buildings', AdminBuildingController::class)->only([
                    'index', 'store', 'destroy', 'show'
                ]);
                Route::post('/buildings/{id}', [AdminBuildingController::class, 'update'])->middleware('auth:sanctum');
                Route::get('buildings_count', [AdminBuildingController::class, 'buildings_count'])->middleware('auth:sanctum');

                Route::resource('users', AdminUserController::class)->only([
                    'index', 'store', 'update', 'destroy', 'show'
                ]);

                Route::resource('denunciations', AdminDenunciationController::class)->only([
                    'index', 'show'
                ]);
                Route::post('/denunciations/{id}', [AdminDenunciationController::class, 'update']);
                Route::get('/denunciations_count', [AdminDenunciationController::class, 'denunciations_count']);
            });
        });

        // Pelapor
        Route::middleware('pelapor')->group(function () {
            Route::prefix('/pelapor')->group(function () {
                Route::resource('denunciations', PelaporDenunciationController::class)->only([
                    'index', 'store', 'show'
                ]);
                Route::post('/denunciations/{id}', [PelaporDenunciationController::class, 'update']);

                Route::resource('type_denunciations', AdminTypeDenunciationController::class)->only([
                    'index'
                ]);
            });
        });


        // Petugas
        Route::middleware('petugas')->group(function () {
            Route::prefix('/petugas')->group(function () {
            });
        });

        // Konsultan
        Route::middleware('konsultan')->group(function () {
            Route::prefix('/petugas')->group(function () {
                Route::resource('buildings', PetugasBuildingController::class)->only([
                    'index', 'store', 'destroy', 'show'
                ]);
                Route::post('/buildings/{id}', [PetugasBuildingController::class, 'update']);

                Route::resource('function_buildings', PetugasFunctionBuildingController::class)->only([
                    'index'
                ]);
            });
        });
    });
});
