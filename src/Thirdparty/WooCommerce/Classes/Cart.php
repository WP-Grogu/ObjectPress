<?php

namespace OP\Thirdparty\WooCommerce\Classes;

use \WooCommerce;

/**
 * This class is used to manage the user cart.
 *
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.5
 * @pattern  Singleton
 */
class Cart
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
    public function get()
    {
        return $this->wc->cart->get_cart();
    }


    /**
     * Add a product to user cart.
     *
     * @param string|int|Model  $product
     * @param int               $quantity       contains the quantity of the item to add.
     * @param int               $variation_id   ID of the variation being added to the cart.
     * @param array             $variation      attribute values.
     * @param array             $cart_item_data extra cart item data we want to pass into the item.
     * @return string|bool      $cart_item_key
     *
     * @throws Exception Plugins can throw an exception to prevent adding to cart.
     */
    public function add($product, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array())
    {
        if (!($product_id = $this->getProductId($product))) {
            return false;
        }

        return $this->wc->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $cart_item_data);
    }


    /**
     * Remove a cart item.
     *
     * @param string|int|Model  $product
     * @param int               $variation_id   ID of the variation being added to the cart.
     * @param array             $variation      attribute values.
     * @param array             $cart_item_data extra cart item data we want to pass into the item.
     *
     * @return bool
     */
    public function remove($product, $variation_id = 0, $variation = array(), $cart_item_data = array())
    {
        if (($id = $this->has($product, $variation_id, $variation, $cart_item_data))) {
            return $this->wc->cart->remove_cart_item($id);
        }
        return false;
    }
    
    
    /**
     * Get the quantity for a given product in the cart.
     *
     * @param string|int|Model $product The product model/id or the product variation id.
     *
     * @return int|null Return quantity if in cart, else return null.
     */
    public function getProductQuantity($product)
    {
        if (!($product_id = $this->getProductId($product))) {
            return null;
        }

        $quantities = $this->wc->cart->get_cart_item_quantities();

        // Return the quantity if targeted product is in cart
        if (isset($quantities[$product_id]) && $quantities[$product_id] > 0) {
            return $quantities[$product_id];
        }

        return null;
    }


    /**
     * Set a quantity for a given cart item id.
     *
     * @param string $product_cart_id  the product cart id obtainable thru Cart::has($product)
     * @param int    $quantity         quantity to set for this item
     * @param bool   $refresh_totals   whether or not to calculate totals after setting the new qty. Can be used to defer calculations if setting quantities in bulk.
     *
     * @return bool
     */
    public function setProductQuantity($product_cart_id, int $quantity = 1, bool $refresh_totals = true)
    {
        if ($product_cart_id) {
            return $this->wc->cart->set_quantity($product_cart_id, $quantity, $refresh_totals);
        }
        return false;
    }


    /**
     * Empties the cart and optionally the persistent cart too.
     *
     * @param bool $clear_persistent_cart Should the persistant cart be cleared too. Defaults to true.
     */
    public function empty(bool $clear_persistent_cart = true)
    {
        return $this->wc->cart->empty_cart($clear_persistent_cart);
    }


    /**
     * Check if the cart has a product.
     *
     * @param string|int|Model  $product
     * @param int               $variation_id   of the product the key is being generated for.
     * @param array             $variation      data for the cart item.
     * @param array             $cart_item_data other cart item data passed which affects this items uniqueness in the cart.
     *
     * @return string Cart id of the product if in the cart, else empty string.
     */
    public function has($product, $variation_id = 0, $variation = array(), $cart_item_data = array())
    {
        if (!($product_id = $this->getProductId($product))) {
            return 0;
        }

        $product_cart_id = $this->getProductCartId($product_id, $variation_id, $variation, $cart_item_data);

        return $this->wc->cart->find_product_in_cart($product_cart_id);
    }


    /**
     * Get the product cart_id, used by WooCommerce to identify the product inside the cart.
     *
     * @param int   $product_id     of the product the key is being generated for.
     * @param int   $variation_id   of the product the key is being generated for.
     * @param array $variation      data for the cart item.
     * @param array $cart_item_data other cart item data passed which affects this items uniqueness in the cart.
     *
     * @return string cart item key
     */
    public function getProductCartId($product_id, $variation_id = 0, $variation = array(), $cart_item_data = array())
    {
        return $this->wc->cart->generate_cart_id($product_id, $variation_id, $variation, $cart_item_data);
    }


    /**
     * Get the product id from various format.
     * Can be model, string, int
     *
     * @return int|null
     */
    private function getProductId($product)
    {
        if (is_int($product) || is_string($product)) {
            return (int) $product;
        }

        if (is_object($product)) {
            return $product->id;
        }

        return null;
    }
}
