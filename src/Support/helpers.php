<?php

if (!function_exists('dd')) {
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
}


if (!function_exists('prd')) {
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
}


if (!function_exists('old')) {
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
}
