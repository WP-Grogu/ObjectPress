<?php

namespace OP\Framework\Api;

use \WP_REST_Request;
use OP\Framework\Contracts\ApiRoute as ApiRouteContract;
use OP\Framework\Factories\ValidatorFactory;
use OP\Framework\Exceptions\FailedInitializationException;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
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
    protected $version = 'v1';


    /**
     * Api route namespace
     *
     * @var string
     */
    protected $namespace;


    /**
     * Api route
     *
     * @var string
     */
    protected $route;
    

    /**
     * HTTP method
     *
     * @var string|array (GET|POST|PUT|DELETE|UPDATE)
     */
    protected $methods = 'GET';


    /**
     * Wordpress API Request object
     *
     * @var \WP_REST_Request
     */
    protected $request;


    /**
     * Route parameters / variables
     *
     * @var array
     */
    protected $args = [];


    /**
     * Route Body parameters / variables
     *
     * @var array
     */
    protected $body_args = [];


    /**
     * Initiate the API route, and expose it
     *
     * @return void
     */
    public function boot()
    {
        extract($this->getRegisterParams());

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
    protected function getRegisterParams()
    {
        $namespace = $this->namespace . '/' . $this->version;
        $route     = (strpos($this->route, '/') === 0) ? $this->route : '/' . $this->route;
        $args      = $this->__getArgs();

        return [
            'namespace'  => $namespace,
            'route'      => $route,
            'parameters' => [
                'methods'             => $this->methods,
                'callback'            => [static::class, '__setup'],
                'args'                => $args,
                'permission_callback' => '__return_true', // TODO: permissions
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
        return (new static())->__boot($request);
    }


    /**
     * Construct the class after an API call
     *
     * @return mixed
     */
    public function __boot(\WP_REST_Request $request)
    {
        $this->request = $request;

        $args      = (object) $this->__getComputedArgs();
        $body_args = (object) $this->__getComputedBodyArgs();

        if (is_a($body_args, 'WP_Error')) {
            return new \WP_REST_Response($body_args, 400);
        }

        return $this->resolve($args, $body_args);
    }


    /**
     * Format register rest api route arguments
     *
     * @return array
     */
    private function __getArgs()
    {
        $args       = $this->args;
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
    protected function __getComputedArgs()
    {
        $params = $this->request->get_params();

        $params = json_decode(json_encode($params));

        return apply_filters('op/f/api/get-computed-args', $params);
    }


    /**
     * Get API body parameters and return them.
     *
     * @return object
     */
    protected function __getComputedBodyArgs()
    {
        $vars   = $this->body_args;
        $body   = json_decode($this->request->get_body(), ARRAY_A);

        // Validate the specified fields.
        foreach ($vars as $var => $args) {
            if (!array_key_exists($var, $body)) {
                continue;
            }

            $validate = $this->__validateBodyParam($var, $body[$var], $args);

            // If there is an error, abort
            if ($validate !== true) {
                return $validate;
            }
        }

        $body = json_decode(json_encode($body));

        do_action('op/a/api/get-computed-body-args', $vars, $body);
        
        return apply_filters('op/f/api/get-computed-body-args', $body);
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
        $list = (new static())->getMergedArgs();

        $rules  = $list[$key]['rules'] ?? null;
        $labels = $list[$key]['label'] ?? $key;

        if (!$rules) {
            return true;
        }

        $input    = [$key => $param];
        $rules    = [$key => $rules];
        $labels   = [$key => $labels];

        $validator  = new ValidatorFactory();

        # Create a validator
        $validation = $validator->make($input, $rules);

        # Set the attributes nice names
        $validation->setAttributeNames(
            $labels
        );

        if ($validation->fails()) {
            return new \WP_Error(
                422,
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
    protected function __validateBodyParam($field, $value, $args)
    {
        if ($call = $args['validate_callback'] ?? null) {
            return $call($value, $this->request, $field);
        }

        return $this->__validate($value, $this->request, $field);
    }



    /**************************/
    /*                        */
    /*        Getters         */
    /*                        */
    /**************************/


    public function getArgs(): array
    {
        return (array) $this->args;
    }
    
    public function getBodyArgs(): array
    {
        return (array) $this->body_args;
    }

    public function getMergedArgs()
    {
        return $this->getArgs() + $this->getBodyArgs();
    }

    public function getBaseUrl(bool $slug_only = false)
    {
        $s = $this->getRegisterParams();

        $slug = sprintf('/%s/%s', trim($s['namespace'], '/'), trim($s['route'], '/'));

        return $slug_only ? $slug : home_url('/wp-json' . $slug);
    }
}
