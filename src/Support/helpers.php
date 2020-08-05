<?php

if (! function_exists('vdd')) {
    /**
     * Dump the passed variables and end the script, using var_dump.
     *
     * @param  mixed
     * @return void
     */
    function vdd()
    {
        array_map(function ($x) {
            var_dump($x);
        }, func_get_args());

        die(1);
    }
}


if (! function_exists('prd')) {
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
