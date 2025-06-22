<?php

namespace maxxidev\Support\Column;

use Illuminate\Support\Facades\Facade;

class Columns extends Facade
{

    protected static function getFacadeAccessor()
    { 
        return ColumnCollection::class; 
    }
}