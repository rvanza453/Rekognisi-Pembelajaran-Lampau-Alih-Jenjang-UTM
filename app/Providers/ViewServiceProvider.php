<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
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
        // Membagikan data user yang sedang login ke view layout 'assessor'
        View::composer('Assessor.*', function ($view) {
            $view->with('loggedInUser', Auth::user());
        });
    }
}
