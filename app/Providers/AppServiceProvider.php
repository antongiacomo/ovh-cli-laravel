<?php

namespace App\Providers;

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
        $localEnv = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';

        if (! file_exists($localEnv) ) {

            $globalEnv = getenv('HOME')
                    . DIRECTORY_SEPARATOR . '.config'
                    . DIRECTORY_SEPARATOR . config('app.name');

            config(['env_path' => $globalEnv ]);

            app()->useEnvironmentPath($globalEnv);
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
