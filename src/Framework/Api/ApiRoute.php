<?php

namespace OP\Framework\Api;

use \WP_REST_Request;
use OP\Framework\Contracts\ApiRouteContract;
use OP\Framework\Factories\ValidatorFactory;
use OP\Framework\Exceptions\FailedInitializationException;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.3
 */
abstract class ApiRoute implements ApiRouteContract
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
    protected static $request;


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
     * Initiate the API route, and expose it
     *
     * @return void
     */
    public static function init()
    {
        extract(static::getRegisterParams());

        if (! register_rest_route($namespace, $route, $parameters)) {
            throw new FailedInitializationException(
                'API route initialization failed for `' . static::class . '`.'
            );
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
        $args      = static::__getArgs();

        return [
            'namespace'  => $namespace,
            'route'      => $route,
            'parameters' => [
                'methods'             => static::$methods,
                'callback'            => [static::class, '__setup'],
                'args'                => $args,
                'permission_callback' => '__return_true',
            ],
        ];
    }


    /**
     * Construct the class after an API call
     *
     * @return mixed
     */
    public static function __setup(\WP_REST_Request $request)
    {
        static::$request = $request;

        $args      = (object) static::__getComputedArgs();
        $body_args = (object) static::__getComputedBodyArgs();

        if (is_a($body_args, 'WP_Error')) {
            return new \WP_REST_Response($body_args, 400);
        }

        return static::resolve($args, $body_args);
    }


    /**
     * Format register rest api route arguments
     *
     * @return array
     */
    private static function __getArgs()
    {
        $args       = static::$args;
        $formated   = [];

        if (empty($args)) {
            return [];
        }

        foreach ($args as $arg_key => $arg_params) {
            $rules      = $arg_params['rules'] ?? [];
            $parameters = [];

            if (!empty($arg_params)) {
                if (is_string($rules)) {
                    $rules = explode('|', $rules);
                }

                $rules = array_map('strtolower', $rules);

                $parameters['required']          = in_array('required', $rules);
                $parameters['validate_callback'] = $arg_params['validate_callback'] ?? [static::class, '__validate'];
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
    protected static function __getComputedArgs()
    {
        $vars   = array_keys(static::$args);
        $params = [];
        
        foreach ($vars as $var) {
            $params[$var] = static::$request->get_param($var);
        }

        $params = json_decode(json_encode($params));

        return apply_filters('op_api_get_computed_args', $params);
    }


    /**
     * Get API body parameters and return them.
     *
     * @return object
     */
    protected static function __getComputedBodyArgs()
    {
        $vars   = static::$body_args;
        $body   = json_decode(static::$request->get_body(), ARRAY_A);

        // Validate the specified fields.
        foreach ($vars as $var => $args) {
            $value = $body[$var] ?? null;

            $validate = static::__validateBodyParam($var, $value, $args);

            // If there is an error, abort
            if ($validate !== true) {
                return $validate;
            }
        }

        do_action('op_api_get_computed_body_args', $vars, $body);
        
        return json_decode(json_encode($body));
    }



    /**************************/
    /*                        */
    /*       Validation       */
    /*                        */
    /**************************/


    /**
     * Validate the inputs based on their according rules.
     *
     * @return true|MessageBag
     */
    public static function __validate($param, $request, $key)
    {
        $list  = (static::$args ?: []) + (static::$body_args ?: []);

        $rules = $list[$key]['rules'] ?? null;

        if (!$rules) {
            return true;
        }

        $input    = [$key => $param];
        $rules    = [$key => $rules];

        $validator  = new ValidatorFactory();

        $validation = $validator->make($input, $rules);

        if ($validation->fails()) {
            return new \WP_Error(
                400,
                implode(", ", $validation->messages()->all())
            );
        }
        
        return true;
    }


    /**
     * Validate body args.
     *
     * @return WP_REST_Response|bool
     */
    protected static function __validateBodyParam($field, $value, $args)
    {
        if (isset($args['validate_callback'])) {
            $call = $args['validate_callback'];

            return $call($value, static::$request, $field);
        }

        return static::__validate($value, static::$request, $field);
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
    public static function getBaseUrl(string $path = '')
    {
        $params = static::getRegisterParams();

        return get_rest_url() . $params['namespace'] . $params['route'] . $path;
    }
}
