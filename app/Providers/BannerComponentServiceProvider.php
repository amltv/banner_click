<?php

namespace App\Providers;

use App\Components\BannerComponent;
use Illuminate\Support\ServiceProvider;

class BannerComponentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('banner', function() {
            return new BannerComponent(config('banner'));
        });
    }
}
