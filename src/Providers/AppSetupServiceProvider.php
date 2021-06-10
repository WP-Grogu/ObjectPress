<?php

namespace OP\Providers;

use OP\Core\Blade;
use OP\Core\Container;
use OP\Core\ObjectPress;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\View\Factory as ViewFactory;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @access   public
 * @version  2.0
 * @since    2.0
 */
class AppSetupServiceProvider extends ServiceProvider
{
    /**
     * The booting method.
     *
     * @return void
     */
    public function register(): void
    {
        // Bootup ObjectPress
        $this->app->instance('object-press', ObjectPress::getInstance());
        
        // Bootup request
        $this->app->instance(Request::class, Request::createFromGlobals());

        // Add Blade.
        $this->app->instance(ViewFactory::class, Blade::getInstance());
        $this->app->alias(ViewFactory::class, 'view');
        $this->app->alias(ViewFactory::class, 'blade');

        // Add Cache
        // TODO: replace cache system
    }
}
