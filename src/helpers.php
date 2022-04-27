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

if (!function_exists('view')) :
    /**
     * Render and print the requested $view with the given $with params.
     *
     * @return void
     */
    function view(string $view, array $with = [])
    {
        echo \OP\Support\Facades\ObjectPress::view()->make($view, $with)->render();
    }
endif;


if (!function_exists('dd')) :
    /**
     * Dump the passed variables and end the script, using var_dump.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        print '<pre>';
        array_map(function ($x) {
            var_dump($x);
        }, func_get_args());
        print '</pre>';

        die(1);
    }
endif;


if (!function_exists('prd')) :
    /**
     * Dump the passed variables and end the script, using print_r.
     *
     * @param  mixed
     * @return void
     */
    function prd()
    {
        print '<pre>';
        array_map(function ($x) {
            print_r($x);
        }, func_get_args());
        print '</pre>';

        die(1);
    }
endif;


if (!function_exists('old')) :
    /**
     * Return the old field value input in forms.
     *
     * @param  string $key Optional. The Request item name. If not specified, returns all values.
     * @return void
     */
    function old(?string $key = null)
    {
        return !is_null($key) ? ($_REQUEST[$key] ?? '') : $_REQUEST;
    }
endif;

if (!function_exists('now')) :
    /**
     * Returns a carbon instance of the very current time.
     *
     * @return \Illuminate\Support\Carbon
     */
    function now()
    {
        return \Illuminate\Support\Carbon::now();
    }
endif;
