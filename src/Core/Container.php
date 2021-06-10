<?php

namespace OP\Core;

use Illuminate\Container\Container as IlluminateContainer;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    1.0.4
 */
class Container
{
    /**
     * The instance
     *
     * @access private
     */
    private static $_instance;


    /**
     * @var Illuminate\Container\Container
     */
    private IlluminateContainer $app;

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
     * prevent the instance from being initied
     */
    private function __construct()
    {
        $this->app = new IlluminateContainer;
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

    /**
     * Handle dynamic, calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (count($args)) {
            case 0:
                return $this->app->$method();

            case 1:
                return $this->app->$method($args[0]);

            case 2:
                return $this->app->$method($args[0], $args[1]);

            case 3:
                return $this->app->$method($args[0], $args[1], $args[2]);

            case 4:
                return $this->app->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$this, $method], $args);
        }
    }
}
