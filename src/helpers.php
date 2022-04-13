<?php

if (!function_exists('controller')) :
    /**
     * Call the given controller.
     * Supports the ps4 notation.
     *
     * @return array
     */
    function controller(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(
                sprintf('You must give an existing class path. `%s` is not a valid class.', $class)
            );
        }

        new $class();
    }
endif;
