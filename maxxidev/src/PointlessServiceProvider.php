<?php

namespace Maxxidev;

use Illuminate\Support\ServiceProvider;

class PointlessServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        resolve(\Illuminate\Routing\UrlGenerator::class)->forceScheme('https');
    }
}
