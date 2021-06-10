<?php

namespace OP\Framework\Wordpress;

use AmphiBee\Hooks\Contracts\Hookable;

abstract class Hook implements Hookable
{
    /**
     * Event name to hook on.
     */
    public $hook = 'wp';
}
