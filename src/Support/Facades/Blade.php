<?php

namespace OP\Support\Facades;

class Blade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'OP\Core\Blade';
    }
}
