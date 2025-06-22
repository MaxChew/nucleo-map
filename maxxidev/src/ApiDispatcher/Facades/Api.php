<?php

namespace Maxxidev\ApiDispatcher\Facades;

use Illuminate\Support\Facades\Facade;
use Maxxidev\ApiDispatcher\Dispatcher;

class Api extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Dispatcher::class;
    }
}
