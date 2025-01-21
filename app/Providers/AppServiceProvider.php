<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Repository::class,
            \App\Repositories\Denunciations::class,
            \App\Repositories\Duties::class,
            \App\Repositories\FunctionBuildings::class,
            \App\Repositories\Buildings::class,
        );

        $this->app->singleton(\App\Services\ApplicationService::class, function ($app) {
            return new \App\Services\ApplicationService();
        });

        $this->app->singleton(\App\Services\BuildingService::class, function ($app) {
            return new \App\Services\BuildingService();
        });

        $this->app->singleton(\App\Services\DenunciationService::class, function ($app) {
            return new \App\Services\DenunciationService();
        });

        $this->app->singleton(\App\Services\DutyService::class, function ($app) {
            return new \App\Services\DutyService();
        });

        $this->app->singleton(\App\Services\FunctionBuildingService::class, function ($app) {
            return new \App\Services\FunctionBuildingService();
        });

        // OUTPUT
        $this->app->singleton(\App\Outputs\Admin\FunctionBuildingOutput::class, function ($app) {
            return new \App\Outputs\Admin\FunctionBuildingOutput();
        });

        $this->app->singleton(\App\Outputs\Admin\TypeDenunciationOutput::class, function ($app) {
            return new \App\Outputs\Admin\TypeDenunciationOutput();
        });

        $this->app->singleton(\App\Outputs\Admin\UserOutput::class, function ($app) {
            return new \App\Outputs\Admin\UserOutput();
        });

        $this->app->singleton(\App\Outputs\Admin\AttachmentOutput::class, function ($app) {
            return new \App\Outputs\Admin\AttachmentOutput();
        });

        $this->app->singleton(\App\Outputs\Admin\DenunciationOutput::class, function ($app) {
            return new \App\Outputs\Admin\DenunciationOutput();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60);
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email'));
        });

    }
}
