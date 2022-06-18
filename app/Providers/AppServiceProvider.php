<?php

namespace App\Providers;

use Dotenv\Dotenv;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! file_exists(base_path() . DIRECTORY_SEPARATOR . '.env') && file_exists(getEnvPath() . DIRECTORY_SEPARATOR . '.env')) {
            $env = Dotenv::createImmutable(getEnvPath())->load();

            config()->set('app.app_key', $env['app_key']);
            config()->set('app.app_secret', $env['app_secret']);
            config()->set('app.consumer_key', $env['consumer_key']);
        }

        app()->singleton('ovh', function(){
            return new \Ovh\Api(
                config('app.app_key'),
                config('app.app_secret'),
                'ovh-eu',
                config('app.consumer_key')
            );
        });
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
