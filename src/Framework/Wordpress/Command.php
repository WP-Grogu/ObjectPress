<?php

namespace OP\Framework\Wordpress;

use WP_CLI;
use OP\Framework\Contracts\WpCliCommand;

abstract class Command implements WpCliCommand
{
    /**
     * The command name called with "wp {name}"
     *
     * @var string
     * @access protected
     */
    protected string $name;


    /**
     * Boot the command into WP-CLI.
     */
    public function boot()
    {
        return WP_CLI::add_command($this->name, [$this, 'execute'], $this->getArgs());
    }


    /**
     * Returns the arguments to pass to the add_command function.
     * An associative array with additional registration parameters.
     *
     * $args (array) {
     *   @type callable $before_invoke Callback to execute before invoking the command.
     *   @type callable $after_invoke Callback to execute after invoking the command.
     *   @type string $shortdesc Short description (80 char or less) for the command.
     *   @type string $longdesc Description of arbitrary length for examples, etc.
     *   @type string $synopsis The synopsis for the command (string or array).
     *   @type string $when Execute callback on a named WP-CLI hook (e.g. before_wp_load).
     *   @type bool $is_deferred Whether the command addition had already been deferred.
     * }
     *
     * @return array
     */
    protected function getArgs(): array
    {
        return [];
    }


    /**
     * Output a success message to the console.
     *
     * @return void
     */
    protected function warning($message)
    {
        return WP_CLI::warning($message);
    }


    /**
     * Output a success message to the console.
     *
     * @return void
     */
    protected function success($message)
    {
        return WP_CLI::success($message);
    }
}
