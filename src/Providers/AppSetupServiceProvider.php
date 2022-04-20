<?php

namespace OP\Providers;

use OP\Core\Blade;
use OP\Core\ObjectPress;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\ServiceProvider;

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
        // Link ObjectPress instance
        $this->app->instance('object-press', ObjectPress::getInstance());

        // Link Blade instance
        // $this->app->instance(ViewFactory::class, Blade::getInstance());
        // $this->app->alias(ViewFactory::class, 'view');
        // $this->app->alias(ViewFactory::class, 'blade');

        // Add Cache
        // TODO: replace cache system
    }
}
