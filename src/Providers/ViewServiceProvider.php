<?php

namespace OP\Providers;

use ReflectionClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use OP\Support\Facades\Config;
use OP\Framework\View\Composer;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\Factory as ViewFactory;

/**
 * The view service provider.
 *
 * @copyright ObjectPress Team, Roots Team
 * @license   MIT
 * @license   https://github.com/acornjs/acorn/blob/master/acorn/LICENSE MIT
 */
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $view = $this->app->make(ViewFactory::class);

        $this->attachComposers($view);
    }

    /**
     * Attach View Composers
     *
     * @return void
     */
    public function attachComposers($view)
    {
        $composers = Config::get('setup.view.composers');
        $paths     = Config::get('object-press.view.paths.composers');
        
        if (is_array($composers) && Arr::isAssoc($composers)) {
            foreach ($composers as $composer) {
                $view->composer($composer::views(), $composer);
            }
        }

        $paths = array_unique($paths ?: []);

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                continue;
            }
            
            foreach ((new Finder())->in($path)->files() as $composer) {
                $composer = ucfirst(str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($composer->getPathname(), get_stylesheet_directory() . DIRECTORY_SEPARATOR)
                ));

                if (
                    class_exists($composer) &&
                    is_subclass_of($composer, Composer::class) &&
                    ! (new ReflectionClass($composer))->isAbstract()
                ) {
                    $view->composer($composer::views(), $composer);
                }
            }
        }
    }
}
