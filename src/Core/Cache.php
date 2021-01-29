<?php

namespace OP\Core;

use Phpfastcache\Helper\Psr16Adapter;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.4
 * @access   public
 * @since    1.0.4
 */
class Cache extends Psr16Adapter
{
    protected static $_driver   = 'Files';
    protected static $_instance = null;

    public static function getInstance()
    {
        if (static::$_instance === null) {
            static::$_instance = new static(static::$_driver);
        }

        return static::$_instance;
    }

    /**
     * prevent the instance from being initied
     */
    private function __construct($driver)
    {
        parent::__construct($driver);
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }
}
