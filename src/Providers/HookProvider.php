<?php

namespace OP\Providers;

use AmphiBee\Hooks\Action;
use AmphiBee\Hooks\Filter;
use OP\Support\Facades\Config;

/**
 * @package  ObjectPress
 * @author   AmphiBee / tgeorgel
 * @access   public
 * @version  2.1
 * @since    2.0
 */
class HookProvider
{
    /**
     * Default hooks settings.
     *
     * @var array
     */
    protected $default = [
        'hook'     => ['init'],
        'priority' => 12,
    ];

    /**
     * The booting method.
     *
     * @return void
     */
    public function boot(): void
    {
        $hooks = Config::get('setup.hooks');

        foreach ($hooks as $type => $classes) {
            foreach ($classes as $class) {
                $reflection = new \ReflectionClass($class);
                $instance   = $reflection->newInstanceWithoutConstructor();
                $args       = array_merge($this->default, get_class_vars(get_class($instance)));

                if (!is_array($args['hook'])) {
                    $args['hook'] = [$args['hook']];
                }

                foreach ($args['hook'] as $hook) {
                    if ($type === 'filter') {
                        Filter::add($hook, $class, $args['priority']);
                    } else {
                        Action::add($hook, $class, $args['priority']);
                    }
                }
            }
        }
    }
}
