<?php

namespace App\Izicrypt\Facade;

use Illuminate\Support\Facades\Facade;

class Izicrypt extends Facade
{
    protected static function getFacadeAccessor() 
    {
        return 'izicrypt';
    }
}