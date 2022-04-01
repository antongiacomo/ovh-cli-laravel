<?php

namespace App;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array get(string $string)
 */
class Ovh extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ovh';
    }
}
