<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use App\Http\Controllers\Auth\AuthorizationController;
// Admin
use App\Http\Controllers\Admin\FunctionBuildingController as AdminFunctionBuildingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\TypeDenunciationController as AdminTypeDenunciationController;
use App\Http\Controllers\Admin\BuildingController as AdminBuildingController;
use App\Http\Controllers\Admin\DenunciationController as AdminDenunciationController;
use App\Http\Controllers\Admin\ArchiveFileController as AdminArchiveFileController;
use App\Http\Controllers\Admin\DutyController as AdminDutyController;
// Pelapor
use App\Http\Controllers\Pelapor\DenunciationController as PelaporDenunciationController;
use App\Http\Controllers\Pelapor\FunctionBuildingController as PelaporFunctionBuildingController;
use App\Http\Controllers\Pelapor\TypeDenunciationController as PelaporTypeDenunciationController;
// Petugas
use App\Http\Controllers\Petugas\BuildingController as PetugasBuildingController;
use App\Http\Controllers\Petugas\FunctionBuildingController as PetugasFunctionBuildingController;
use App\Http\Controllers\Petugas\DutyController as PetugasDutyController;
use Illuminate\Support\Facades\Storage;

Route::middleware([
    EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
])->group(function () {
    Route::post('login', [AuthorizationController::class, 'login']);

    Route::middleware('auth:sanctum')->get('/storage/{path_file}/{file}', function ($path_file, $file) {
        $path = storage_path('/app/public/'.$path_file.'/'.$file);
        dd($path);
        return response()->file($path, [
                'Content-Type' => mime_content_type($path),
            ])->setStatusCode(200);

        //$fullPath = trim($path_file . '/' . $file, '/');

        //Log::info("Mencoba mengakses file: $fullPath");

        //if (!Storage::disk('public')->exists($fullPath)) {
        //    Log::error("File $fullPath tidak ditemukan");
        //    abort(404);
        //}

        //Log::info("File ditemukan. Mengirim dengan status 200.");
        //return response()->file(Storage::disk('public')->path($fullPath))
        //    ->setStatusCode(200);

    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthorizationController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('me', [AuthorizationController::class, 'me'])->middleware('auth:sanctum');
        Route::post('change_profile', [AuthorizationController::class, 'change_profile'])->middleware('auth:sanctum');

        // Admin
        Route::middleware('admin')->group(function () {
            Route::prefix('/admin')->group(function () {
                Route::resource('function_buildings', AdminFunctionBuildingController::class)->only([
                    'index', 'store', 'update', 'destroy', 'show'
                ]);

                Route::resource('type_denunciations', AdminTypeDenunciationController::class)->only([
                    'index', 'store', 'update', 'destroy', 'show'
                ]);

                Route::get('/cluster/buildings', [AdminBuildingController::class, 'cluster']);
                Route::resource('buildings', AdminBuildingController::class)->only([
                    'index', 'store', 'destroy', 'show'
                ]);
                Route::post('/buildings/{id}', [AdminBuildingController::class, 'update'])->middleware('auth:sanctum');
                Route::get('/buildings/count/building_permit', [AdminBuildingController::class, 'count_building_permit'])->middleware('auth:sanctum');
                Route::get('/buildings/export/excel', [AdminBuildingController::class, 'export_excel']);

                Route::resource('archive_files', AdminArchiveFileController::class)->only([
                    'index', 'store', 'destroy', 'show'
                ]);
                Route::post('/archive_files/{id}', [AdminArchiveFileController::class, 'update'])->middleware('auth:sanctum');

                Route::resource('users', AdminUserController::class)->only([
                    'index', 'store', 'update', 'destroy', 'show'
                ]);

                Route::resource('denunciations', AdminDenunciationController::class)->only([
                    'index', 'show'
                ]);
                Route::get('/cluster/denunciations', [AdminDenunciationController::class, 'cluster']);
                Route::post('/denunciations/{id}', [AdminDenunciationController::class, 'update']);
                Route::get('/denunciations/count/by_new_and_in_progress', [AdminDenunciationController::class, 'count_by_new_and_in_progress']);
                Route::get('/denunciations/count/every_state_by_month_year', [AdminDenunciationController::class, 'count_every_state_by_month_year']);
                Route::get('/denunciations/count/done_by_year', [AdminDenunciationController::class, 'count_done_by_year']);
                Route::get('/denunciations/export/excel', [AdminDenunciationController::class, 'export_excel']);

                Route::resource('duties', AdminDutyController::class)->only([
                    'index', 'show'
                ]);
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

                Route::resource('type_denunciations', PelaporTypeDenunciationController::class)->only([
                    'index'
                ]);

                Route::resource('function_buildings', PelaporFunctionBuildingController::class)->only([
                    'index'
                ]);
            });
        });


        // Petugas
        Route::middleware('petugas')->group(function () {
            Route::prefix('/petugas')->group(function () {
                Route::resource('duties', PetugasDutyController::class)->only([
                    'index', 'show'
                ]);
                Route::post('/duties/{id}/start', [PetugasDutyController::class, 'start']);
                Route::post('/duties/{id}/submit', [PetugasDutyController::class, 'submit']);
            });
        });

        Route::middleware(['konsultan_petugas'])->group(function () {
            Route::prefix('/petugas')->group(function () {
                Route::get('/buildings', [PetugasBuildingController::class, 'index']);
                Route::get('/buildings/{id}', [PetugasBuildingController::class, 'show']);
                Route::get('/buildings/export/excel', [PetugasBuildingController::class, 'export_excel']);

                Route::resource('/function_buildings', PetugasFunctionBuildingController::class)->only([
                    'index'
                ]);
            });
        });
        // Konsultan
        Route::middleware('konsultan')->group(function () {
            Route::prefix('/petugas')->group(function () {

                Route::post('/buildings', [PetugasBuildingController::class, 'store']);
                Route::post('/buildings/{id}', [PetugasBuildingController::class, 'update']);
                Route::delete('/buildings/{id}', [PetugasBuildingController::class, 'destroy']);
            });
        });
    });
});
