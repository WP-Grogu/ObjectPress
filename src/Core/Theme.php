<?php

namespace OP\Core;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.0
 * @access   public
 * @since    7.0
 */
final class Theme
{
    private static $_instance = null;

    private function __construct()
    {
        $this->addSupport('title-tag')
             ->addSupport('custom-logo')
             ->addSupport('post-thumbnails')
             ->addSupport('customize-selective-refresh-widgets')
             ->addSupport('html5', [
                 'search-form',
                 'comment-form',
                 'comment-list',
                 'gallery',
                 'caption'
             ])
             ->addStyle('theme-styles', get_stylesheet_uri())
             ->addCommentScript();
    }


    /**
     * Returns instance, creates one if needed
     *
     * @param  void
     * @return Theme
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Theme();
        }

        return self::$_instance;
    }


    private function actionAfterSetup($function)
    {
        add_action('after_setup_theme', function () use ($function) {
            $function();
        });
    }

    private function actionEnqueueScripts($function)
    {
        add_action('wp_enqueue_scripts', function () use ($function) {
            $function();
        });
    }


    public function addSupport($feature, $options = null)
    {
        $this->actionAfterSetup(function () use ($feature, $options) {
            if ($options) {
                add_theme_support($feature, $options);
            } else {
                add_theme_support($feature);
            }
        });
        return $this;
    }

    public function removeSupport($feature)
    {
        $this->actionAfterSetup(function () use ($feature) {
            remove_theme_support($feature);
        });
        return $this;
    }

    public function loadTextDomain($domain, $path = false)
    {
        $this->actionAfterSetup(function () use ($domain, $path) {
            load_theme_textdomain($domain, $path);
        });
        return $this;
    }

    public function addImageSize($name, $width = 0, $height = 0, $crop = false)
    {
        $this->actionAfterSetup(function () use ($name, $width, $height, $crop) {
            add_image_size($name, $width, $height, $crop);
        });
        return $this;
    }

    public function removeImageSize($name)
    {
        $this->actionAfterSetup(function () use ($name) {
            remove_image_size($name);
        });
        return $this;
    }

    public function addStyle($handle, $src = '', $deps = array(), $ver = false, $media = 'all')
    {
        $this->actionEnqueueScripts(function () use ($handle, $src, $deps, $ver, $media) {
            wp_enqueue_style($handle, $src, $deps, $ver, $media);
        });
        return $this;
    }

    public function addScript($handle, $src = '', $deps = array(), $ver = false, $in_footer = false)
    {
        $this->actionEnqueueScripts(function () use ($handle, $src, $deps, $ver, $in_footer) {
            wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
        });
        return $this;
    }

    public function addCommentScript()
    {
        $this->actionEnqueueScripts(function () {
            if (is_singular() && comments_open() && get_option('thread_comments')) {
                wp_enqueue_script('comment-reply');
            }
        });
        return $this;
    }

    public function removeStyle($handle)
    {
        $this->actionEnqueueScripts(function () use ($handle) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        });
        return $this;
    }

    public function removeScript($handle)
    {
        $this->actionEnqueueScripts(function () use ($handle) {
            wp_dequeue_script($handle);
            wp_deregister_script($handle);
        });
        return $this;
    }

    public function addNavMenus($locations = array())
    {
        $this->actionAfterSetup(function () use ($locations) {
            register_nav_menus($locations);
        });
        return $this;
    }

    public function addNavMenu($location, $description)
    {
        $this->actionAfterSetup(function () use ($location, $description) {
            register_nav_menu($location, $description);
        });
        return $this;
    }

    public function removeNavMenu($location)
    {
        $this->actionAfterSetup(function () use ($location) {
            unregister_nav_menu($location);
        });
        return $this;
    }


    /**
     * WP Action generate_rewrite_rules
     *
     * @param array $new_rules Array containing rules to add
     *
     * @return $this
     * @chainable
     */
    public function addRewriteRules(array $new_rules)
    {
        $this->on('generate_rewrite_rules', function ($wp_rewrite) use ($new_rules) {
            $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
        });
        return $this;
    }


    /**
     * Add a filter call on specified hook on wp stack.
     *
     * @param string   $filter        Hook tag to bind function to
     * @param callable $function      Action to be executed
     * @param int      $priority      Used to specify the order in which the functions associated with a particular action are executed
     * @param int      $accepted_args The number of arguments the function accepts.
     *
     * @return $this
     * @chainable
     * @reference https://developer.wordpress.org/reference/functions/add_filter/
     */
    public function addFilter(string $filter, callable $function, int $priority = 10, int $accepted_args = 1)
    {
        add_filter($filter, $function, $priority, $accepted_args);

        return $this;
    }


    /**
     * Add an action call on specified hook on wp stack.
     *
     * @param string   $action        Hook name to bind function
     * @param callable $function      Action to be executed
     * @param int      $priority      Used to specify the order in which the functions associated with a particular action are executed
     * @param int      $accepted_args The number of arguments the function accepts.
     *
     * @return $this
     * @chainable
     * @reference https://developer.wordpress.org/reference/functions/add_action/
     */
    public function addAction(string $action, callable $function, int $priority = 10, int $accepted_args = 1)
    {
        add_action($action, $function, $priority, $accepted_args);

        return $this;
    }

    /**
     * Add an action call on specified hook on wp stack.
     *
     * @param string|array  $action        Hook name to bind function
     * @param callable      $function      Action to be executed
     * @param int           $priority      Used to specify the order in which the functions associated with a particular action are executed
     * @param int           $accepted_args The number of arguments the function accepts.
     *
     * @return $this
     * @chainable
     * @reference https://developer.wordpress.org/reference/functions/add_action/
     */
    public function on($actions, callable $function, int $priority = 10, int $accepted_args = 1)
    {
        if (is_array($actions)) {
            foreach ($actions as $action) {
                add_action($action, $function, $priority, $accepted_args);
            }
        } elseif (is_string($actions)) {
            add_action($actions, $function, $priority, $accepted_args);
        }

        return $this;
    }
}
