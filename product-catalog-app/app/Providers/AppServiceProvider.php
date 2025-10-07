<?php
namespace App\Providers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\ProductRepositoryInterface::class,
            \App\Repositories\ProductRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\CategoryRepositoryInterface::class,
            \App\Repositories\CategoryRepository::class
        );
        
        $this->app->bind(
            \App\Services\FileUploadServiceInterface::class,
            function ($app) {
                return new \App\Services\FileUploadService(
                    Storage::disk('public')
                );
            }
        );
    }

    public function boot(): void
    {
        //
    }
}
