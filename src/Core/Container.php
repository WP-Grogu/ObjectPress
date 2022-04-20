<?php

namespace OP\Core;

use Illuminate\View\Factory;
use OP\Support\Facades\Config;
use Illuminate\Events\Dispatcher;
use Illuminate\View\FileViewFinder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Compilers\BladeCompiler;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Container\Container as IlluminateContainer;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    1.0.4
 */
class Container
{
    /**
     * The instance
     *
     * @var Container
     * @access private
     */
    private static $_instance;

    /**
     * The Container instance
     *
     * @var \Illuminate\Container\Container
     */
    private IlluminateContainer $container;

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
     * prevent the instance from being initied
     */
    private function __construct()
    {
        $container = new IlluminateContainer;

        // Tell facade about the application instance.
        Facade::setFacadeApplication($container);

        // Enable HTTP Method Override.
        Request::enableHttpMethodParameterOverride();

        // Register application instance with container
        $container['app'] = $container;

        // Set environment.
        $container['env'] = defined('WP_ENV') ? WP_ENV : 'production';

        // Create the request.
        $container['request'] = Request::createFromGlobals();

        // Link instances.
        $container->instance(\Symfony\Component\HttpFoundation\Request::class, $container['request']);
        $container->instance(\Illuminate\Contracts\Foundation\Application::class, $container);

        $this->container = $container;
        
        $this->bootstrapBlade();
    }

    /**
     * Bootstrap the blade engine compiler.
     *
     * @return void
     */
    private function bootstrapBlade()
    {
        $inputs = collect(Config::get('object-press.template.blade.inputs'));
        $output = collect(Config::get('object-press.template.blade.output'));

        $inputs = $inputs->filter()->unique()->toArray();
        $output = $output->filter()->unique()->first();

        if (!is_dir($output)) {
            mkdir($output, 0770, true);
        }

        // Dependencies
        $filesystem = new Filesystem;
        $eventDispatcher = new Dispatcher($this->container);

        // Create View Factory capable of rendering PHP and Blade templates
        $viewResolver = new EngineResolver;
        $bladeCompiler = new BladeCompiler($filesystem, $output);

        $viewResolver->register('blade', fn () => new CompilerEngine($bladeCompiler));

        $viewFinder = new FileViewFinder($filesystem, $inputs);
        $viewFactory = new Factory($viewResolver, $viewFinder, $eventDispatcher);
        $viewFactory->setContainer($this->container);

        $this->container->instance(\Illuminate\Contracts\View\Factory::class, $viewFactory);
        $this->container->alias(
            \Illuminate\Contracts\View\Factory::class,
            (new class extends \Illuminate\Support\Facades\View {
                public static function getFacadeAccessor()
                {
                    return parent::getFacadeAccessor();
                }
            })::getFacadeAccessor()
        );

        $this->container->instance(\Illuminate\View\Compilers\BladeCompiler::class, $bladeCompiler);
        $this->container->alias(
            \Illuminate\View\Compilers\BladeCompiler::class,
            (new class extends \Illuminate\Support\Facades\Blade {
                public static function getFacadeAccessor()
                {
                    return parent::getFacadeAccessor();
                }
            })::getFacadeAccessor()
        );
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    public function getContainerInstance()
    {
        return $this->container;
    }

    /**
     * Handle dynamic, calls to the container instance thru class calling.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (count($args)) {
            case 0:
                return $this->container->$method();

            case 1:
                return $this->container->$method($args[0]);

            case 2:
                return $this->container->$method($args[0], $args[1]);

            case 3:
                return $this->container->$method($args[0], $args[1], $args[2]);

            case 4:
                return $this->container->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$this->container, $method], $args);
        }
    }
}
