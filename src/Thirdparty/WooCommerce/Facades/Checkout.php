<?php

namespace OP\Thirdparty\WooCommerce\Facades;

use OP\Support\Facades\Facade;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.5
 */
class Checkout extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'OP\Thirdparty\WooCommerce\Classes\Checkout';
    }
}
