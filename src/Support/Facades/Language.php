<?php

namespace OP\Support\Facades;

class Language extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'OP\Support\Language\Language';
    }
}
