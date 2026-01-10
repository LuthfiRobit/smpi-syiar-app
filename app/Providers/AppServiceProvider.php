<?php

namespace App\Providers;

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

    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $school_identity = \App\Models\SchoolIdentity::first();
            $view->with('school_identity', $school_identity);
        });
    }
}
