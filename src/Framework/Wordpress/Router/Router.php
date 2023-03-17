<?php

namespace OP\Framework\Wordpress\Router;

use WP_Error;

class Router
{
    /**
     * All registered routes.
     *
     * @var array
     */
    private $routes;

    /**
     * Query variable used to identify routes.
     *
     * @var string
     */
    private $route_variable;

    /**
     * Constructor.
     *
     * @param array $routes
     */
    public function __construct($route_variable = 'route_name', array $routes = [])
    {
        $this->routes         = [];
        $this->route_variable = $route_variable;

        foreach ($routes as $name => $route) {
            $this->add_route($name, $route);
        }
    }

    /**
     * Add a route to the router.
     *
     * @param \App\Classes\Route $route
     */
    public function add_route($name, Route $route)
    {
        $this->routes[$name] = $route;
    }

    /**
     * Adds a new WordPress rewrite rule for the given Route.
     *
     * @param string $name
     * @param Route  $route
     * @param string $position
     */
    private function add_rule($name, Route $route, $position = 'top')
    {
        $query  = 'index.php?'.$this->route_variable.'='.$name;
        $query .= $route->has_query() ? '&'.$route->get_query() : '';

        add_rewrite_rule($this->generate_route_regex($route), $query, $position);
    }

    /**
     * Generates the regex for the WordPress rewrite API for the given route.
     *
     * @param Route $route
     *
     * @return string
     */
    private function generate_route_regex(Route $route)
    {
        return '^'.ltrim(trim($route->get_path()), '/').'$';
    }

    /**
     * Add a new rewrite tag.
     */
    public function add_tag(string $tag, string $regex = '(.+)', string $query = '')
    {
        add_rewrite_tag('%'.$tag.'%', $regex, $query);
    }

    /**
     * Compiles the router into WordPress rewrite rules.
     */
    public function compile()
    {
        $this->add_tag('%'.$this->route_variable.'%', '(.+)');

        foreach ($this->routes as $name => $route) {
            $this->add_rule($name, $route);
        }
    }

    /**
     * Tries to find a matching route using the given query variables. Returns the matching route
     * or a WP_Error.
     *
     * @param array $query_variables
     *
     * @return Route|WP_Error
     */
    public function match(array $query_variables)
    {
        if (empty($query_variables[$this->route_variable])) {
            return new WP_Error('missing_route_variable');
        }

        $route_name = $query_variables[$this->route_variable];

        if (!isset($this->routes[$route_name])) {
            return new WP_Error('route_not_found');
        }

        return $this->routes[$route_name];
    }
}
