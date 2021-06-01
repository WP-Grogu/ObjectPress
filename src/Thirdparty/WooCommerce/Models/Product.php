<?php

namespace OP\Thirdparty\WooCommerce\Models;

use OP\Framework\Models\PostModel;
use OP\Framework\Exceptions\MethodNotFoundException;
use OP\Thirdparty\WooCommerce\Exceptions\WcProductNotFoundException;
use \WooCommerce;

/**
 * This model is meant to be used to interact with a WooCommerce product.
 * This model includes all ObjectPress WP model methods, and some specificities for WooCommerce.
 *
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.5
 */
class Product extends PostModel
{
    /**
     * Wordpress post_type associated to the current model
     */
    public static $post_type = 'product';

    /**
     * The WooCommerce instance.
     */
    protected $wc;

    /**
     * The WooCommerce product.
     */
    protected $wc_product;


    /**
     * Contructor used to build the Model in order to use WooCommerce.
     *
     * @return void
     */
    protected function _modelConstructor()
    {
        $this->wc = WooCommerce::instance();

        if (!$this->wc_product) {
            $this->wc_product = $this->wc->product_factory->get_product($this->id);

            if (!$this->wc_product) {
                throw new WcProductNotFoundException(
                    "WooCommerce did not found a product with id {$this->id}."
                );
            }
        }
    }


    /**
     * Get the WC_Product associated to this model.
     *
     * @param string|array $class — One or more classes to add to the class list.
     *
     * @return WC_Product|WC_Product_Simple
     */
    public function getWcProductClass($class = '')
    {
        return wc_product_class($class, $this->id);
    }


    /**
     * Get the WC_Product property.
     *
     * @param string Property to get. Eg: for 'get_price', property is 'price'
     *
     * @return mixed
     */
    public function getData(string $property)
    {
        $method_name = "get_$property";

        if (!method_exists($this->wc_product, $method_name)) {
            throw new MethodNotFoundException(
                "Could not get WC Product property `$property` : method $method_name not found."
            );
        }

        return $this->wc_product->$method_name();
    }


    /**
     * Get all WC_Product properties.
     *
     * @return array
     */
    public function getDatas(): array
    {
        $method_name = "get_data";

        if (!method_exists($this->wc_product, $method_name)) {
            throw new MethodNotFoundException(
                "Could not get WC Product properties : method $method_name not found."
            );
        }

        return $this->wc_product->$method_name() ?: [];
    }


    /**
     * Return the product id.
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->id;
    }


    /**
     * Return the product variation id if existing.
     *
     * @return int|bool The variation id if allowed, else false.
     */
    public function getProductVariationId()
    {
        if ($this->isType('variable')) {
            return $this->wc_product->get_variation_id();
        }
        return false;
    }


    /**
     * Check if the product if of the given type.
     *
     * @param string $type Type to check (i.e. variable or simple)
     *
     * @return bool
     */
    public function isType($type)
    {
        return $this->wc_product->is_type($type);
    }
}
