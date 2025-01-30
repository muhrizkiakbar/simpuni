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
use App\Http\Controllers\Admin\DenunciationController as PetugasDenunciController;

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
                Route::get('/buildings/count/building_permit', [AdminBuildingController::class, 'count_building_permit'])->middleware('auth:sanctum');

                Route::resource('users', AdminUserController::class)->only([
                    'index', 'store', 'update', 'destroy', 'show'
                ]);

                Route::resource('denunciations', AdminDenunciationController::class)->only([
                    'index', 'show'
                ]);
                Route::post('/denunciations/{id}', [AdminDenunciationController::class, 'update']);
                Route::get('/denunciations/count/by_new_and_in_progress', [AdminDenunciationController::class, 'count_by_new_and_in_progress']);
                Route::get('/denunciations/count/every_state_by_month_year', [AdminDenunciationController::class, 'count_every_state_by_month_year']);
                Route::get('/denunciations/count/done_by_year', [AdminDenunciationController::class, 'count_done_by_year']);
            });
        });

        // Pelapor
        Route::middleware('pelapor')->group(function () {
            Route::prefix('/pelapor')->group(function () {
                Route::resource('denunciations', PelaporDenunciationController::class)->only([
                    'index', 'store', 'show'
                ]);
                Route::post('/denunciations/{id}', [PelaporDenunciationController::class, 'update']);
                Route::get('/denunciations/count/in_progress', [PelaporDenunciationController::class, 'count_denunciation_in_progress']);

                Route::resource('type_denunciations', AdminTypeDenunciationController::class)->only([
                    'index'
                ]);

                Route::resource('function_buildings', PetugasFunctionBuildingController::class)->only([
                    'index'
                ]);
            });
        });


        // Petugas
        Route::middleware('petugas')->group(function () {
            Route::prefix('/petugas')->group(function () {
                Route::resource('denunciations', PetugasDenunciationController::class)->only([
                    'index', 'show'
                ]);
                Route::post('/denunciations/{id}/start', [PetugasDenunciationController::class, 'start']);
                Route::post('/denunciations/{id}/submit', [PetugasDenunciationController::class, 'start']);
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
