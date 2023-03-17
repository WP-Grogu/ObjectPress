<?php

namespace OP\Thirdparty\WooCommerce\Classes;

use \WooCommerce;

/**
 * This class is used to manage the checkout.
 *
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.5
 * @pattern  Singleton
 */
class Checkout
{
    /**
     * Class instance.
     */
    private static $_instance = null;

    /**
     * The WooCommerce instance.
     */
    protected $wc;


    /**
     * Gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance()
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }


    /**
     * Contructor used to build the Model in order to use WooCommerce.
     *
     * @return void
     */
    private function __construct()
    {
        $this->wc = WooCommerce::instance();
    }


    /**
     * Return the user cart.
     */
    public function getFields()
    {
        return $this->wc->checkout->checkout_fields;
    }
}
