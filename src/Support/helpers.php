<?php

if (! function_exists('dd')) {
    /**
     * Dump the passed variables and end the script, using var_dump.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($x) {
            var_dump($x);
        }, func_get_args());

        die(1);
    }
}


if (! function_exists('pd')) {
    /**
     * Dump the passed variables and end the script, using print_r.
     *
     * @param  mixed
     * @return void
     */
    function pd()
    {
        print '<pre>';
        array_map(function ($x) {
            print_r($x);
        }, func_get_args());
        print '</pre>';
        
        die(1);
    }
}
