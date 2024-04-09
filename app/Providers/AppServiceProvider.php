<?php

namespace App\Providers;

use App\Models\Board;
use App\Observers\BoardObserver;
use Illuminate\Support\ServiceProvider;

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
        Board::observe(BoardObserver::class);
    }
}
