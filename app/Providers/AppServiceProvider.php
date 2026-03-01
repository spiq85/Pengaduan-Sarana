<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\InputAspirations;
use Illuminate\Support\Facades\Route;
use App\Models\Aspirations;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::model('input', InputAspirations::class);
        Route::model('aspiration', Aspirations::class);
        Paginator::useBootstrapFive();

        // Event listeners are auto-discovered by Laravel 11
        // (CreateInitialProgress, SendProgressNotification)
    }
}
