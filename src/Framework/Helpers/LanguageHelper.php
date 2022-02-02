<?php

namespace OP\Framework\Helpers;

use OP\Support\Facades\ObjectPress;
use OP\Framework\Contracts\LanguageDriver;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    1.0.0
 */
class LanguageHelper
{
    /**
     * Get the language driver.
     *
     * @return LanguageDriver|null
     */
    protected function getDriver()
    {
        $app = ObjectPress::app();

        # No supported lang plugin detected
        if (!$app->bound(LanguageDriver::class)) {
            return null;
        }

        return $app->make(LanguageDriver::class);
    }


    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $driver = (new LanguageHelper)->getDriver();

        if (!$driver) {
            return null;
        }

        switch (count($args)) {
            case 0:
                return $driver->$method();

            case 1:
                return $driver->$method($args[0]);

            case 2:
                return $driver->$method($args[0], $args[1]);

            case 3:
                return $driver->$method($args[0], $args[1], $args[2]);

            case 4:
                return $driver->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$driver, $method], $args);
        }
    }
}
