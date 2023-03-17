<?php

namespace OP\Providers;

use Illuminate\Support\Str;
use OP\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @access   public
 * @version  2.1.1
 * @since    2.1.1
 */
class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * The booting method.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerIntervals();
        $this->registerEvents();
    }

    /**
     * Register the schedules/intervals to Wordpress.
     *
     * @return void
     */
    protected function registerIntervals()
    {
        $intervals = Config::get('setup.schedule.intervals') ?: [];

        $intervals = collect($intervals)->mapWithKeys(fn ($settings) => [
            $settings['name'] => [
                'interval' => $settings['interval'],
                'display'  => __($settings['label']),
            ],
        ]);

        if ($intervals->isNotEmpty()) {
            add_filter('cron_schedules', fn ($schedules) => $intervals->toArray() + $schedules);
        }
    }

    /**
     * Register the events to Wordpress.
     *
     * @return void
     */
    protected function registerEvents()
    {
        $events = Config::get('setup.schedule.events') ?: [];

        foreach ($events as $event) {
            if ($when = $event['when'] ?? false) {
                if (!(is_callable($when) ? $when() : $when)) {
                    continue;
                }
            }

            $callee   = $this->extractCallee($event);
            $interval = $event['interval'] ?? null;

            if ($callee && $interval) {
                $action = $event['as'] ?? $this->generateActionName($callee);

                $this->registerAction($action, $callee);

                if (!wp_next_scheduled($action)) {
                    wp_schedule_event(time(), $interval, $action);
                }
            }
        }
    }

    /**
     * Given an avent config array, find out the event callee.
     *
     * @return array|string|null The callee or null if not found.
     */
    protected function extractCallee(array $event)
    {
        if (($callee = $event['function'] ?? false)) {
            if (is_array($callee)) {
                return count($callee) === 2 && method_exists(...$callee)
                            ? $callee
                            : null;
            }

            return function_exists($callee)
                        ? $callee
                        : null;
        }
        
        if (($callee = $event['class'] ?? false)) {
            return class_exists($callee) && method_exists($callee, 'runSchedule')
                            ? $callee
                            : null;
        }

        return null;
    }

    /**
     * Generate an action name for a given event based on the callee.
     *
     * @return string
     */
    protected function generateActionName($callee)
    {
        if (is_array($callee)) {
            $callee = implode('_', $callee);
        }

        return Str::snake(Str::camel(Str::replace('\\', ' ', $callee)));
    }

    /**
     * Register a schedule action from the event action name & callee.
     *
     * @return void
     */
    protected function registerAction(string $action, $callee)
    {
        add_action($action, function () use ($callee) {
            if (is_array($callee)) {
                list($class, $method) = $callee;
                return (new $class())->{$method}();
            }
            
            if (function_exists($callee)) {
                return $callee();
            }

            return (new $callee())->runSchedule();
        });
    }
}
