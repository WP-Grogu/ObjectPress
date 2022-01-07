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
     * Bootup Command classes
     */
    public function __construct()
    {
        # Enable error displaying for debugging on local env or if WP_DEBUG is true
        if ((defined('WP_DEBUG') && WP_DEBUG) || (defined('WP_ENV') && WP_ENV === 'development')) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);
        }
    }


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
     * Display informational message without prefix, or discarded when --quiet argument is supplied.
     *
     * @return void
     */
    protected function log($message)
    {
        return WP_CLI::log($message);
    }

    /**
     * Display informational message without prefix, and ignores --quiet argument.
     *
     * @return void
     */
    protected function line($message)
    {
        return WP_CLI::line($message);
    }

    /**
     * Display warning message prefixed with “Warning: “.
     *
     * @return void
     */
    protected function warning($message)
    {
        return WP_CLI::warning($message);
    }

    /**
     * Display error message prefixed with “Error: “, and exits script.
     *
     * @return void
     */
    protected function error($message)
    {
        return WP_CLI::error($message);
    }

    /**
     * Display success message prefixed with “Success: “.
     *
     * @return void
     */
    protected function success($message)
    {
        return WP_CLI::success($message);
    }

    /**
     * Display debug message prefixed with “Debug: ” when --debug argument is supplied.
     *
     * @return void
     */
    protected function debug($message)
    {
        return WP_CLI::debug($message);
    }
}
