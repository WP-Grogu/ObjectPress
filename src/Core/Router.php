<?php

namespace OP\Core;

use OP\Support\Facades\ObjectPress;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @access   public
 * @since    2.0
 */
class Router
{
    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected static $container;

    /**
     * Mark if the router has been bootstrapped.
     *
     * @var boolean
     */
    protected static $bootstrapped = false;

    /**
     * Mark if the request has been dispatched.
     *
     * @var boolean
     */
    protected static $dispatched = false;

    /**
     * Create a new router instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bootstrap();
    }

    public static function bootstrap($errorCallbacks = null)
    {
        // Only bootstrap once.
        if (static::$bootstrapped) {
            return;
        }

        $app = ObjectPress::app();
        static::$container = $app;

        $route_files = [
            '/routes/web.php',
            '/routes/api.php',
        ];

        foreach ($route_files as $file) {
            $path = get_template_directory() . $file;
            
            if (file_exists($path)) {
                require_once $path;
            }
        }

        // Dispatch on shutdown.
        register_shutdown_function('OP\Core\Router::dispatch', $errorCallbacks);

        // Mark bootstrapped.
        static::$bootstrapped = true;
    }

    /**
     * Dispatch the current request to the application.
     *
     * @return \Illuminate\Http\Response
     */
    public static function dispatch($callbacks)
    {
        // Only dispatch once.
        if (static::$dispatched) {
            return;
        }

        // Get the request.
        $request = static::$container['request'];

        try {
            // Pass the request to the router.
            $response = static::$container['router']->dispatch($request);

            // Send the response.
            $response->send();
        } catch (NotFoundHttpException $ex) {
            $callback = is_array($callbacks) ? $callbacks['not_found'] : $callbacks;
            call_user_func($callback, $ex);
        } catch (MethodNotAllowedHttpException $ex) {
            $callback = is_array($callbacks) ? $callbacks['not_allowed'] : $callbacks;
            call_user_func($callback, $ex);
        }

        // Mark as dispatched.
        static::$dispatched = true;
    }

    /**
     * Dynamically pass calls to the router instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array(static::$container['router'], $method), $parameters);
    }
}
