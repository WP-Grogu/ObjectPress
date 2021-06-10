<?php

namespace OP\Support;

use OP\Framework\Exceptions\ClassNotFoundException;
use OP\Framework\Exceptions\MethodNotFoundException;

class Boot
{
    /**
     * Get the registered name of the component.
     *
     * @return void
     */
    public static function single(string $class, string $method = 'boot')
    {
        if (! class_exists($class)) {
            throw new ClassNotFoundException("ObjectPress initialisation : Class `$class` does not exists.");
        }

        if (! method_exists($class, $method)) {
            throw new MethodNotFoundException("ObjectPress initialisation : Class `$class` does not have an `$method()` method.");
        }

        (new $class)->$method();
    }


    /**
     * Get the registered name of the component.
     *
     * @return void
     */
    public static function array(array $classes, string $method = 'boot')
    {
        foreach ($classes as $class) {
            static::single($class, $method);
        }
    }
}
