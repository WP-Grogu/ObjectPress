<?php

namespace OP\Core;

use OP\Core\Patterns\SingletonPattern;
use OP\Support\Facades\Config;
use OP\Support\Facades\Theme;

final class ObjectPress
{
    use SingletonPattern;

    private $asset_path;

    /**
     * Is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
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
     * Initiate app/theme instance
     * Init CPTs, taxos, apis.. using config/app.php config file
     *
     * @return void
     */
    public function init()
    {
        // Init Custom post types & Taxonomies
        Theme::on('init', function () {
            $this->initClasses(Config::get('app.cpts') ?: []);
            $this->initClasses(Config::get('app.taxonomies') ?: []);
        });
        
        // Init Api routes
        Theme::on('rest_api_init', function () {
            $this->initClasses(Config::get('app.apis') ?: []);
        });
    }


    /**
     * Initiale ObjectPress hooks
     */
    private function initHooks()
    {
        // TODO: force term selection in metaboxes
        // // Enable jQuery
        // Theme::on('admin_init', function () {
        //     wp_enqueue_script('jquery');
        // });
        
        // // Enable Add force taxonomy selection
        // Theme::on('edit_form_advanced', function () {
        //     if (file_exists(($path = $this->asset_path . '/html/taxonomies.html'))) {
        //         echo file_get_contents($path);
        //     }
        // });
    }


    /**
     * Include helpers functions
     *
     * @return void
     */
    private function includeHelpers()
    {
        require_once realpath(__DIR__ . '/../Support/helpers.php');
    }

    
    /**
     * Given an array of classes, will try to init them thru init() method
     *
     * @param array $classes Array of classes to initiate
     * @return void
     */
    private function initClasses(array $classes)
    {
        if (empty($classes)) {
            return;
        }
        
        foreach ($classes as $class) {
            if (! class_exists($class)) {
                throw new \Exception("OP : Init : Class `$class` does not exists.");
            }

            if (! method_exists($class, 'init')) {
                throw new \Exception("OP : Init : Class `$class` does not have an init() method.");
            }

            $class::init();
        }
    }
}
