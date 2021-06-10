<?php

namespace OP\Core;

use OP\Providers\HookProvider;
use OP\Support\Facades\Config;
use Phpfastcache\CacheManager;
use OP\Providers\LanguageProvider;
use OP\Core\Patterns\SingletonPattern;
use OP\Providers\AppSetupServiceProvider;
use OP\Providers\LanguageServiceProvider;
use Phpfastcache\Config\ConfigurationOption;
use As247\WpEloquent\Capsule\Manager as Capsule;
use OP\Framework\Exceptions\FileNotFoundException;
use Illuminate\Contracts\Container\Container as ContainerContract;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    1.0.3
 */
final class ObjectPress
{
    use SingletonPattern;

    private $asset_path;

    /**
     * @var OP\Core\Container
     */
    private Container $app;

    /**
     * Is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
        $this->app = Container::getInstance();

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
        if (!defined('OBJECTPRESS_ROOT_DIR')) {
            define('OBJECTPRESS_ROOT_DIR', realpath(__DIR__ . '/../../'));
        }

        if (!defined('OP_DEFAULT_I18N_DOMAIN_CPTS')) {
            define('OP_DEFAULT_I18N_DOMAIN_CPTS', 'op-theme-cpts');
        }

        if (!defined('OP_DEFAULT_I18N_DOMAIN_TAXOS')) {
            define('OP_DEFAULT_I18N_DOMAIN_TAXOS', 'op-theme-taxos');
        }
        
        if (!defined('OP_DEFAULT_I18N_DOMAIN_ROLES')) {
            define('OP_DEFAULT_I18N_DOMAIN_ROLES', 'op-theme-roles');
        }

        if (!defined('DATERANGE_1_DAY')) {
            define('DATERANGE_1_DAY', 86400);
        }
        
        if (!defined('DATERANGE_1_HOUR')) {
            define('DATERANGE_1_HOUR', 3600);
        }
        
        if (!defined('DATERANGE_1_MINUTE')) {
            define('DATERANGE_1_MINUTE', 60);
        }
    }


    /**
     * Initiate app/theme instance
     * Init CPTs, taxos, apis.. using config/app.php config file
     *
     * @deprecated Use boot() instead.
     * @return void
     */
    public function init()
    {
        return $this->boot();
    }


    /**
     * Initiate app/theme instance
     * Init CPTs, taxos, apis.. using config/app.php config file
     *
     * @return void
     */
    public function boot()
    {
        // Initiate capsule (wpEloquent, https://github.com/as247/wp-eloquent)
        // Capsule::bootWp();

        (new AppSetupServiceProvider)->register();
        (new LanguageServiceProvider)->register();
        (new HookProvider)->boot();

        // Setup cache system
        $this->bootCache();
    }


    /**
     * Include helpers functions
     *
     * @return void
     */
    private function includeHelpers()
    {
        $rel_paths = [
            '/../Support/helpers.php',
        ];

        foreach ($rel_paths as $rel_path) {
            $path = realpath(__DIR__ . $rel_path);

            if (!$path) {
                throw new \Exception("OP : includeHelpers : Missing core helpers files.");
            }

            require_once $path;
        }
    }

    
    /**
     * Given an array of classes, will try to init them uning bootup method
     *
     * @param array $classes Array of classes to initiate
     * @return void
     */
    public function initClasses(array $classes, string $method = 'init')
    {
        if (empty($classes)) {
            return;
        }
        
        foreach ($classes as $class) {
            if (! class_exists($class)) {
                throw new \Exception("ObjectPress initialisation : Class `$class` does not exists.");
            }

            if (! method_exists($class, $method)) {
                throw new \Exception("ObjectPress initialisation : Class `$class` does not have an `$method()` method.");
            }

            $class::$method();
        }
    }


    /**
     * Has ObjectPress been Initiated ?
     *
     * @return bool
     */
    public function isInitiated()
    {
        return isset(static::$_instance) && !is_null(static::$_instance);
    }


    /**
     * Setup the cache parameters in order to use cache at needs.
     *
     * @return void
     */
    private function bootCache()
    {
        if (!Config::get('object-press.cache.active')) {
            return;
        }

        // Setup cache folder
        $cache_rel_path = Config::get('object-press.cache.path');

        if (!realpath($cache_rel_path) && !mkdir($cache_rel_path, 0700, true)) {
            throw new FileNotFoundException(
                "ObjectPress error : The specified cache folder $cache_rel_path doesn't exists. Please create it or check your configuration file ({theme}/config/object-press.php)."
            );
        }

        CacheManager::setDefaultConfig(new ConfigurationOption([
            'path' => realpath($cache_rel_path),
        ]));
    }


    /**
     * Get ObjectPress app container.
     *
     * @return OP\Core\Container
     */
    public function app()
    {
        return $this->app;
    }


    /**
     * Set ObjectPress container.
     *
     * @param ContainerContract
     * @return this
     */
    public function setContainer(Container $container)
    {
        $this->app = $container;
        return $this;
    }
}
