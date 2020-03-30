<?php

namespace PHPJuice\Blueprint\Facades;

use Illuminate\Support\Facades\Facade;

class Blueprint extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'blueprint';
    }
}
