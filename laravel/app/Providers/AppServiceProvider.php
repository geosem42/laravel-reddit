<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
