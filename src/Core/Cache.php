<?php

namespace OP\Core;

use Phpfastcache\Helper\Psr16Adapter;
use OP\Support\Facades\Config;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    1.0.4
 */
class Cache extends Psr16Adapter
{
    /**
     * The instance
     *
     * @access private
     */
    private static $_instance;


    /**
     * Get the Cache instance.
     *
     * @return self
     */
    public static function getInstance()
    {
        if (!Config::get('object-press.cache.active')) {
            throw new \RuntimeException(
                "ObjectPress : You must enable cache in order to use the Cache system."
            );
        }

        if (static::$_instance === null) {
            $driver = Config::get('object-press.cache.driver') ?: 'Files';
            static::$_instance = new static($driver);
        }

        return static::$_instance;
    }

    /**
     * prevent the instance from being initied
     */
    private function __construct($driver)
    {
        parent::__construct($driver);
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }
}
