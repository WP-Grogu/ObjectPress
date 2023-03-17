<?php

namespace OP\Framework\Wordpress\Router;

class Route
{
    /**
     * The hook called when this route is matched.
     *
     * @var string
     */
    private $hook;

    /**
     * The URL path that the route needs to match.
     *
     * @var string
     */
    private $path;

    /**
     * The template that the route wants to load.
     *
     * @var string
     */
    private $template;
    
    /**
     * The controller that the route wants to load.
     *
     * @var string
     */
    private $controller;

    /**
     * The query match when using regex.
     *
     * @var string
     */
    private $query;

    /**
     * Constructor.
     *
     * @param string $path
     * @param string $hook
     * @param string $template
     * @param string $query
     */
    public function __construct($path, $hook = '', $template = '', $query = '')
    {
        $this->path     = $path;
        $this->hook     = $hook;
        $this->template = $template;
        $this->query    = $query;
    }

    /**
     * Static constructor for method chaining.
     *
     * @param string $path
     * @param string $hook
     * @param string $template
     * @param string $query
     */
    public static function make($path, $hook = '', $template = '', $query = '')
    {
        return new static($path, $hook, $template, $query);
    }

    /**
     * Get the hook called when this route is matched.
     *
     * @return string
     */
    public function get_hook()
    {
        return $this->hook;
    }

    /**
     * Get the URL path that the route needs to match.
     *
     * @return string
     */
    public function get_path()
    {
        return $this->path;
    }

    /**
     * Get the template that the route wants to load.
     *
     * @return string
     */
    public function get_template()
    {
        return $this->template;
    }

    /**
     * Get the query that the route must match.
     *
     * @return string
     */
    public function get_query()
    {
        return $this->query;
    }
    
    /**
     * Get the controller that the route must load.
     *
     * @return string
     */
    public function get_controller()
    {
        return $this->controller;
    }

    /**
     * Checks if this route want to call a hook when matched.
     *
     * @return bool
     */
    public function has_hook()
    {
        return !empty($this->hook);
    }

    /**
     * Checks if this route want to load a template when matched.
     *
     * @return bool
     */
    public function has_template()
    {
        return !empty($this->template);
    }
    
    /**
     * Checks if this route want to load a controller when matched.
     *
     * @return bool
     */
    public function has_controller()
    {
        return !empty($this->controller);
    }
    
    /**
     * Checks if this route needs to match a query.
     *
     * @return bool
     */
    public function has_query()
    {
        return !empty($this->query);
    }

    /**
     * Set the hook called when this route is matched.
     *
     * @return self
     */
    public function set_hook($query)
    {
        $this->hook = $query;
        return $this;
    }

    /**
     * Set the URL path that the route needs to match.
     *
     * @return self
     */
    public function set_path(string $path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set the template that the route wants to load.
     *
     * @return self
     */
    public function set_template(string $template)
    {
        $this->template = $template;
        return $this;
    }
   
    /**
     * Set the controller that the route wants to load.
     *
     * @return self
     */
    public function set_controller(string $controller)
    {
        if (class_exists($controller)) {
            $this->controller = $controller;
        }
        return $this;
    }

    /**
     * Set the query that the route must match.
     *
     * @return self
     */
    public function set_query(string $query)
    {
        $this->query = $query;
        return $this;
    }
}
