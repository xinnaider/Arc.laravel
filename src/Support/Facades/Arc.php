<?php

namespace Fernando\Arc\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Arc extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'arc';
    }
}
