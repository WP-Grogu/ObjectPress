<?php

namespace OP\Support\Language;

use OP\Support\Facades\ObjectPress;
use OP\Framework\Contracts\LanguageDriver;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    2.0
 *
 * @singleton
 */
class Language
{
    /**
     * The singleton instance.
     *
     * @var static
     */
    private static $_instance;

    /**
     * The language driver.
     *
     * @var LanguageDriver
     */
    protected LanguageDriver $driver;
    

    /**
     * Gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance()
    {
        if (static::$_instance === null) {
            static::$_instance = new static(
                ObjectPress::app()->make(LanguageDriver::class)
            );
        }

        return static::$_instance;
    }


    /**
     * Class constructor.
     *
     * @return void
     */
    private function __construct(LanguageDriver $driver)
    {
        $this->driver = $driver;
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
                return $this->driver->$method();

            case 1:
                return $this->driver->$method($args[0]);

            case 2:
                return $this->driver->$method($args[0], $args[1]);

            case 3:
                return $this->driver->$method($args[0], $args[1], $args[2]);

            case 4:
                return $this->driver->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$this, $method], $args);
        }
    }


    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }
}
