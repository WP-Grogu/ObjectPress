<?php

namespace OP\Lib\WpEloquent\Facades;

use OP\Lib\WpEloquent\Database;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\Database\DatabaseManager
 * @see \Illuminate\Database\Connection
 */
class DB extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Database::instance();
    }
}
