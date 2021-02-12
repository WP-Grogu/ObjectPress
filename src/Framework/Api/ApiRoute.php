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
     * Route Body parameters / variables
     *
     * @var array
     */
    public static $body_args = [];


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
                'callback' => [static::class, 'resolve____op'],
                'args'     => $args
            ]
        ];
    }


    /**
     * Construct the class after an API call
     *
     * @return mixed
     */
    public static function resolve____op(\WP_REST_Request $request)
    {
        static::$request = $request;

        $args      = (object) static::getComputedArgs();
        $body_args = (object) static::getComputedBodyArgs();

        if (is_a($args, 'WP_REST_Response')) {
            return $args;
        }
        if (is_a($body_args, 'WP_REST_Response')) {
            return $body_args;
        }

        return static::resolve($args, $body_args);
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
     * Get API parameters and return them.
     *
     * @return object
     */
    protected static function getComputedArgs()
    {
        $vars   = array_keys(static::$args);
        $params = [];

        foreach ($vars as $var) {
            $params[$var] = static::$request->get_param($var);
        }

        do_action('op_api_get_computed_args', $vars);

        return json_decode(json_encode($params));
    }


    /**
     * Get API body parameters and return them.
     *
     * @return object
     */
    protected static function getComputedBodyArgs()
    {
        $vars   = static::$body_args;
        $body   = json_decode(static::$request->get_body(), ARRAY_A);
        $params = [];

        foreach ($vars as $var => $args) {
            $value = $body[$var] ?? null;

            $validate = static::validateBodyParam($var, $value, $args);

            if (is_a($validate, 'WP_REST_Response')) {
                return $validate;
            }

            $params[$var] = $value;
        }

        do_action('op_api_get_computed_body_args', $vars, $body);
        
        return json_decode(json_encode($params));
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
    public static function validateString($param, $request = null, $key = null)
    {
        return is_string($param);
    }


    /**
     * Validate the param as an email
     *
     * @return bool
     */
    public static function validateEmail($param, $request = null, $key = null)
    {
        return is_string($param) && filter_var($param, FILTER_VALIDATE_EMAIL);
    }


    /**
     * Validate the param as a int
     *
     * @return bool
     */
    public static function validateInteger($param, $request = null, $key = null)
    {
        return preg_match("/^\d+$/", $param);
    }


    /**
     * Validate body args.
     *
     * @return WP_REST_Response|bool
     */
    protected static function validateBodyParam($field, $value, $args)
    {
        if (isset($args['required']) && $args['required']) {
            if (!$value || empty($value)) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => 'The field "' . $field . '" is mandatory on your body request.',
                ], 400);
            }

            if (isset($args['type'])) {
                $method = static::class . '::validate' . ucfirst(strtolower($args['type']));
    
                if (!$method($value)) {
                    return new \WP_REST_Response([
                        'success' => false,
                        'message' => 'The field "' . $field . '" must be a "'. $args['type'] .'" type.',
                    ], 400);
                }
            }
        }

        return true;
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
