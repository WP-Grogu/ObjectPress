<?php

namespace OP\Core\Hooks;

use OP\Support\Boot;
use OP\Support\Facades\Config;
use OP\Framework\Wordpress\Hook;

class SetupApi extends Hook
{
    /**
     * Event name to hook on.
     */
    public $hook = 'rest_api_init';


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
            Config::get('setup.apis') ?: [],
        ];

        Boot::array(array_merge(...$listing));
    }
}
