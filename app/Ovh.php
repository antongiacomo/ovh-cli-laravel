<?php

namespace App;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self with(Downloadble $downloadble)
 */
class Ovh extends Facade
{
    protected static function getFacadeAccessor()
    {
       return 'ovh';
    }
}
