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
     * @var Container
     * @access private
     */
    private static $_instance;

    /**
     * The Container instance
     *
     * @var Illuminate\Container\Container
     */
    private IlluminateContainer $container;

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
        $this->container = new IlluminateContainer;
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
     * Handle dynamic, calls to the container instance thru class calling.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (count($args)) {
            case 0:
                return $this->container->$method();

            case 1:
                return $this->container->$method($args[0]);

            case 2:
                return $this->container->$method($args[0], $args[1]);

            case 3:
                return $this->container->$method($args[0], $args[1], $args[2]);

            case 4:
                return $this->container->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$this->container, $method], $args);
        }
    }
}
