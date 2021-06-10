<?php

namespace OP\Core\Hooks;

use OP\Support\Facades\ObjectPress;
use OP\Support\Facades\Config;
use OP\Framework\Wordpress\Hook;

class SetupGraphql extends Hook
{
    /**
     * Event name to hook on.
     */
    public $hook = 'graphql_register_types';


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
            Config::get('setup.gql-types') ?: [],
            Config::get('setup.gql-fields') ?: [],
        ];

        foreach ($listing as $classes) {
            ObjectPress::initClasses($classes, 'init');
        }
    }
}
