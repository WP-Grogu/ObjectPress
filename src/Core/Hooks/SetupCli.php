<?php

namespace OP\Core\Hooks;

use OP\Support\Boot;
use OP\Support\Facades\Config;
use OP\Framework\Wordpress\Hook;

class SetupCli extends Hook
{
    /**
     * Event name to hook on.
     */
    public $hook = 'cli_init';


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
        $commands = Config::get('setup.commands') ?: [];

        Boot::array($commands);
    }
}
