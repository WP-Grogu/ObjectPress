<?php

namespace OP\Core;

use OP\Core\Patterns\Singleton;

final class Container
{
    use Singleton;

    /**
     * Define OP constants in case they are not defined yet
     * 
     * @return void
     */
    private function setupConstants()
    {
        if (!defined('OP_DEFAULT_I18N_DOMAIN_CPTS')) {
            define('OP_DEFAULT_I18N_DOMAIN_CPTS', 'op-theme-cpts');
        }

        if (!defined('OP_DEFAULT_I18N_DOMAIN_TAXOS')) {
            define('OP_DEFAULT_I18N_DOMAIN_TAXOS', 'op-theme-taxos');
        }
    }


    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
        $this->setupConstants();
    }
}
