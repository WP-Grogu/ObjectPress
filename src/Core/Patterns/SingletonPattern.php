<?php

namespace OP\Core\Patterns;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.2
 * @access   public
 * @since    1.0.2
 */
trait SingletonPattern
{
    private static $_instance = null;

    /**
     * Gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance()
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }
}
