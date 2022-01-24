<?php

namespace OP\Providers;

use OP\Framework\Contracts\LanguageDriver;
use OP\Support\Language\Drivers\WPMLDriver;
use OP\Support\Language\Drivers\PolylangDriver;
use Illuminate\Support\ServiceProvider;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @access   public
 * @version  2.0
 * @since    2.0
 */
class LanguageServiceProvider extends ServiceProvider
{
    /**
     * The booting method.
     *
     * @return void
     */
    public function register(): void
    {
        $driver = $this->getLanguageDriver();

        if ($driver) {
            $this->app->bind(LanguageDriver::class, $driver);
            $this->app->alias(LanguageDriver::class, 'language');
        }
    }


    /**
     * Find out which driver to use based on activated plugin.
     * Supports WPML & Polylang.
     *
     * @return string|null The class to use
     */
    private function getLanguageDriver()
    {
        $driver = null;

        # Make sure to include is_plugin_active()
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        # PolyLang
        if (!$driver && (is_plugin_active('polylang/polylang.php') || is_plugin_active('polylang-pro/polylang.php'))) {
            $driver = PolylangDriver::class;
        }
        
        # WPML
        if (!$driver && (is_plugin_active('sitepress-multilingual-cms/sitepress.php') || is_plugin_active('wpml-multilingual-cms/sitepress.php'))) {
            $driver = WPMLDriver::class;
        }

        /**
         * Allow driver modification from filter.
         * The driver MUST implements the LanguageDriver contract.
         */
        return apply_filters('op/providers/language/default_driver', $driver);
    }
}
