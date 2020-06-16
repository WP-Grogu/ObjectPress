<?php

namespace OP\Core;

use OP\Core\Patterns\SingletonPattern;
use OP\Framework\Theme;

final class Container
{
    use SingletonPattern;

    private $asset_path;

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
        $this->theme = Theme::getInstance();

        $this->asset_path = realpath(__DIR__ . '/../../assets');

        $this->includeHelpers();
        $this->setupConstants();
    }


    /**
     * Define OP constants in case they are not defined yet
     *
     * @return void
     */
    private function setupConstants()
    {
        if (!defined('OP_DEFAULT_I18N_DOMAIN_CPTS')) {
            define('OP_DEFAULT_I18N_DOMAIN_CPTS', 'op-theme-cpts');
        }

        if (!defined('OP_DEFAULT_I18N_DOMAIN_TAXOS')) {
            define('OP_DEFAULT_I18N_DOMAIN_TAXOS', 'op-theme-taxos');
        }
    }


    /**
     * Initiale ObjectPress hooks
     */
    private function initHooks()
    {
        // TODO: force term selection in metaboxes
        // // Enable jQeuery
        // $this->theme->on('admin_init', function () {
        //     wp_enqueue_script('jquery');
        // });
        
        // // Enable Add force taxonomy selection
        // $this->theme->on('edit_form_advanced', function () {
        //     if (file_exists(($path = $this->asset_path . '/html/taxonomies.html'))) {
        //         echo file_get_contents($path);
        //     }
        // });
    }


    public function includeHelpers()
    {
        require_once realpath(__DIR__ . '/../Support/helpers.php');
    }
}
