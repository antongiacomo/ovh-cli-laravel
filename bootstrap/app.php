<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new LaravelZero\Framework\Application(
    dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Set the correct path for the environment file
|--------------------------------------------------------------------------
|
| The default environment file is usually located within the project root
| directory. However, once you export the build of your application
| you can move the environment file to `~/.config/ovhcli/.env`.
|
*/

if (! file_exists(base_path() . DIRECTORY_SEPARATOR . '.env')) {
    $app->useEnvironmentPath(getEnvPath());
}

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    LaravelZero\Framework\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Illuminate\Foundation\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
