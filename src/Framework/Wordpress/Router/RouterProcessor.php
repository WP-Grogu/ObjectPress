<?php

namespace OP\Framework\Wordpress\Router;

use WP;
use WP_Error;

class RouterProcessor
{
    /**
     * The router.
     *
     * @var Router
     */
    private $router;

    /**
     * The routes we want to register with WordPress.
     *
     * @var Route[]
     */
    private $routes;
    
    /**
     * The routes we want to register with WordPress.
     *
     * @var Route|null
     */
    private $matched_route;

    /**
     * Constructor.
     *
     * @param Router  $router
     * @param Route[] $routes
     */
    public function __construct(Router $router, array $routes = [])
    {
        $this->router = $router;
        $this->routes = $routes;
    }

    /**
     * Initialize processor with WordPress.
     *
     * @param Router  $router
     * @param Route[] $routes
     */
    public static function init(Router $router, array $routes = [])
    {
        $self = new self($router, $routes);
        $self->boot();
    }

    /**
     * Initialize processor with WordPress.
     */
    public function boot()
    {
        add_action('init', [$this, 'register_routes']);
        add_action('parse_request', [$this, 'match_request']);
        add_action('template_include', [$this, 'load_route_template']);
        add_action('template_redirect', [$this, 'call_route_hook']);
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
     * Add a new rewrite tag.
     */
    public function add_tag(string $tag, string $regex = '(.+)', string $query = '')
    {
        $this->router->add_tag($tag, $regex, $query);
    }

    /**
     * Register all our routes into WordPress.
     */
    public function register_routes()
    {
        $routes = apply_filters('theme_routes', $this->routes);

        foreach ($routes as $name => $route) {
            $this->router->add_route($name, $route);
        }

        $this->router->compile();

        $routes_hash = md5(serialize($routes));

        if ($routes_hash != get_option('theme_routes_hash')) {
            flush_rewrite_rules();
            update_option('theme_routes_hash', $routes_hash);
        }
    }

    /**
     * Attempts to match the current request to a route.
     *
     * @param WP $environment
     */
    public function match_request(WP $environment)
    {
        $matched_route = $this->router->match($environment->query_vars);

        if ($matched_route instanceof Route) {
            $this->matched_route = $matched_route;
        }
    }

    /**
     * Checks to see if a route was found. If there's one, it calls the route hook.
     */
    public function call_route_hook()
    {
        if (!$this->matched_route instanceof Route || !$this->matched_route->has_hook()) {
            return;
        }

        do_action($this->matched_route->get_hook());
    }

    /**
     * Checks to see if a route was found. If there's one, it loads the route template.
     *
     * @param string $template
     *
     * @return string
     */
    public function load_route_template($template)
    {
        if (!$this->matched_route instanceof Route) {
            return $template;
        }

        # Match a controller
        if ($this->matched_route->has_controller()) {
            controller($this->matched_route->get_controller());
            return '';
        }
        # Match a template
        elseif ($this->matched_route->has_template()) {
            $route_template = get_query_template($this->matched_route->get_template());
    
            if (!empty($route_template)) {
                $template = $route_template;
            }
        }

        return $template;
    }
}
