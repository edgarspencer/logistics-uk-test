<?php

namespace App\Providers;

use App\Repositories\TaskRepository;
use App\Services\TaskService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository
        $this->app->bind(TaskRepository::class, function ($app) {
            return new TaskRepository();
        });
        
        // Bind service
        $this->app->bind(TaskService::class, function ($app) {
            return new TaskService(
                $app->make(TaskRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}