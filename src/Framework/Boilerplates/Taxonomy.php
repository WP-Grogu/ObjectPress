<?php

namespace OP\Framework\Boilerplates;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.1
 * @access   public
 * @since    0.1
 */
abstract class Taxonomy
{
    /**
     * Domain name for string translation
     *
     * @var string
     * @since 0.1
     */
    protected static $domain;


    /**
     * Taxonomy name
     *
     * @var string
     * @since 0.1
     */
    protected static $taxonomy = 'custom-taxonomy';

    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     * @since 0.1
     */
    public static $singular = 'custom-taxonomy';
    public static $plural   = 'custom-taxonomies';


    /**
     * Enable graphql on this taxonomy
     *
     * @var bool
     * @since 0.1
     */
    public static $graphql_enabled = false;


    /**
     * Register this taxonomy on thoses post types
     *
     * @var array
     * @since 0.1
     */
    protected static $post_types = [];


    /**
     * Class constructor, register Taxonomy to wordpress
     *
     * @param array $args   Optionnal. Args to overide.
     * @param array $labels Optionnal. Labels to override.
     *
     * @return void
     * @since 0.1
     */
    public static function register(array $args = [], array $labels = [])
    {
        if (taxonomy_exists(static::$taxonomy)) {
            return;
        }

        $singular   = static::$singular;
        $plural     = static::$plural;

        $base_labels = [
            'name'              => _x("$plural", 'taxonomy general name', '148-taxonomies'),
            'singular_name'     => _x("$singular", 'taxonomy singular name', '148-taxonomies'),
            'search_items'      =>  __("Search $plural", '148-taxonomies'),
            'all_items'         => __("All $plural", '148-taxonomies'),
            'parent_item'       => __("Parent $singular", '148-taxonomies'),
            'parent_item_colon' => __("Parent $singular:", '148-taxonomies'),
            'edit_item'         => __("Edit $singular", '148-taxonomies'),
            'update_item'       => __("Update $singular", '148-taxonomies'),
            'add_new_item'      => __("Add New $singular", '148-taxonomies'),
            'new_item_name'     => __("New $singular Name", '148-taxonomies'),
            'menu_name'         => __("$plural", '148-taxonomies'),
        ];

        // Override default labels with the specified custom ones
        $labels = array_replace($base_labels, $labels);

        $base_args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
        ];

        // If graphql is enabled, we need to setup some more params
        if (static::$graphql_enabled) {
            $base_args = array_replace($base_args, [
                'show_in_graphql'       => true,
                'graphql_single_name'   => static::graphqlFormatName($singular),
                'graphql_plural_name'   => static::graphqlFormatName($plural),
                'graphql_singular_type' => static::graphqlFormatName($singular),
                'graphql_plural_type'   => static::graphqlFormatName($plural),
            ]);
        }

        // Override default args with the specified custom ones
        $args = array_replace($base_args, $args);

        register_taxonomy(static::$taxonomy, static::$post_types, $args);
    }


    /**
     * Returns Taxonomy's domain for string translation
     *
     * @return string
     * @since 0.1
     */
    public function getDomain()
    {
        return static::$domain;
    }


    /**
     * Convert Taxonomy names to graphql format
     * eg: 'Étude de cas' => 'etudeDeCas'
     *
     * @param string
     * @return string
     * @since 0.1
     */
    protected static function graphqlFormatName(string $type)
    {
        return lcfirst(preg_replace('/\s/', '', ucwords(str_replace('-', ' ', sanitize_title($type)))));
    }
}
