<?php

namespace App\Support;

use Illuminate\Support\Facades\Facade;

class Defaults extends Facade
{

    protected static function getFacadeAccessor()
    {
        return DefaultManager::class;
    }

}