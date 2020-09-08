<?php

namespace OP\Framework\Api;

use OP\Framework\Interfaces\IApiRoute;
use \WP_REST_Request;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.4
 * @access   public
 * @since    1.0.4
 */
abstract class ApiRoute implements IApiRoute
{
    /**
     * Api version
     *
     * @var string
     */
    public static $version = 'v1';


    /**
     * Api route namespace
     *
     * @var string
     */
    public static $namespace;


    /**
     * Api route
     *
     * @var string
     */
    public static $route;
    

    /**
     * HTTP method
     *
     * @var string|array (GET|POST|PUT|DELETE|UPDATE)
     */
    public static $methods = 'GET';


    /**
     * Wordpress API Request object
     *
     * @var \WP_REST_Request
     */
    public static $request;


    /**
     * Route parameters / variables
     *
     * @var array
     */
    public static $args = [];


    /**
     * API call parameters to be filled on API request
     *
     * @var array
     */
    public static $params = [];


    /**
     * Default argument parameter
     *
     * @var array
     */
    protected static $default_arg = [
        'required' => false,
        'type' => 'String',
    ];


    /**
     * Initiate the API route, and expose it
     *
     * @return void
     */
    public static function init()
    {
        extract(static::getRegisterParams());

        if (! register_rest_route($namespace, $route, $parameters)) {
            throw new \Exception('ObjectPress: API initialisation failed for `' . static::class . '`.');
        }
    }


    /**
     * Get the parameters for register_rest_route()
     *
     * @return array
     */
    protected static function getRegisterParams()
    {
        $namespace = static::$namespace . '/' . static::$version;
        $route     = (strpos(static::$route, '/') === 0) ? static::$route : '/' . static::$route;
        $args      = static::getArgs();

        return [
            'namespace'  => $namespace,
            'route'      => $route,
            'parameters' => [
                'methods'  => static::$methods,
                'callback' => [static::class, 'constructor'],
                'args'     => $args
            ]
        ];
    }


    /**
     * Construct the class after an API call
     *
     * @return mixed
     */
    public static function constructor(\WP_REST_Request $request)
    {
        static::$request = $request;
        static::setParams();

        return static::resolve();
    }


    /**
     * Format register rest api route arguments
     *
     * @return array
     */
    private static function getArgs()
    {
        $args       = static::$args;
        $formated   = [];

        if (empty($args)) {
            return [];
        }

        foreach ($args as $arg_key => $arg_params) {
            $arg_params = $arg_params + static::$default_arg;
            $parameters = [];

            if ($arg_params['required'] === true) {
                $parameters['required'] = true;
            }

            if (isset($arg_params['validate_callback'])) {
                $parameters['validate_callback'] = $arg_params['validate_callback'];
            } else {
                $type = isset($arg_params['type']) ? strtolower($arg_params['type']) : 'string';

                if ($type !== 'undefined') {
                    $parameters['validate_callback'] = [static::class, 'validate' . ucfirst($type)];
                }
            }

            $formated[$arg_key] = $parameters;
        }

        return $formated;
    }


    /**
     * Get API parameters and store them into static::$params
     *
     * @return void
     */
    protected static function setParams()
    {
        $vars = array_keys(static::$args);

        foreach ($vars as $var) {
            static::$params[$var] = static::$request->get_param($var);
        }
    }



    /**************************/
    /*                        */
    /*       Validation       */
    /*                        */
    /**************************/


    /**
     * Validate the param as a string
     *
     * @return bool
     */
    public static function validateString($param, $request, $key)
    {
        return is_string($param);
    }


    /**
     * Validate the param as a int
     *
     * @return bool
     */
    public static function validateInteger($param, $request, $key)
    {
        return preg_match("/^\d+$/", $param);
    }



    /**************************/
    /*                        */
    /*        Helpers         */
    /*                        */
    /**************************/


    /**
     * Returns register_rest_route parameters
     *
     * @return array
     */
    public static function debugParams()
    {
        return static::getRegisterParams();
    }
    

    /**
     * Get ApiRoute base url
     *
     * @return string
     */
    public static function getBaseUrl($path = '/')
    {
        $params = static::getRegisterParams();

        return get_rest_url() . $params['namespace'] . $params['route'] . $path;
    }
}
