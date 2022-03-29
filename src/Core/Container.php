<?php

namespace OP\Core;

use Illuminate\Container\Container as IlluminateContainer;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Facade;

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
        $app = new IlluminateContainer;

        // Tell facade about the application instance.
        // Facade::setFacadeApplication($app);

        // Register application instance with container
        $app['app'] = $app;

        // Set environment.
        $app['env'] = defined('WP_ENV') ? WP_ENV : 'production';

        // Enable HTTP Method Override.
        Request::enableHttpMethodParameterOverride();

        // Create the request.
        $app['request'] = Request::createFromGlobals();

        // Link request instance.
        $app->instance(Request::class, $app['request']);

        $this->container = $app;
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    public function getContainerInstance()
    {
        return $this->container;
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
