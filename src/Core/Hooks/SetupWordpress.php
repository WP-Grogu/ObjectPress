<?php

namespace OP\Core\Hooks;

use OP\Support\Boot;
use OP\Support\Facades\Config;
use OP\Framework\Wordpress\Hook;

class SetupWordpress extends Hook
{
    /**
     * Event name to hook on.
     */
    public $hook = 'init';


    /**
     * Hook Priority
     */
    public $priority = 13;


    /**
     * The actions to perform.
     *
     * @return void
     */
    public function execute(): void
    {
        $listing = [
            Config::get('setup.cpts') ?: [],
            Config::get('setup.taxonomies') ?: [],
            Config::get('setup.user-roles') ?: [],
        ];

        Boot::array(array_merge(...$listing));
    }
}
