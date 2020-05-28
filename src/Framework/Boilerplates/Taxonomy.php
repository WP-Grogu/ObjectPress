<?php

namespace OP\Framework\Boilerplates;

use OP\Lib\TaxonomySingleTerm\TaxonomySingleTerm;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.3
 * @access   public
 * @since    0.1
 */
abstract class Taxonomy
{
    /********************************/
    /*                              */
    /*        Default params        */
    /*                              */
    /********************************/


    /**
     * Domain name for string translation
     *
     * @var string
     * @since 0.1
     */
    protected static $domain = 'theme-taxos';


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
    public static $singular = 'Custom Taxonomy';
    public static $plural   = 'Custom Taxonomies';


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
     * Taxonomy argument to overide over boilerplate
     *
     * @var array
     * @since 1.3
     */
    public static $args_override = [];


    /**
     * Taxonomy labels to overide over boilerplate
     *
     * @var array
     * @since 1.3
     */
    public static $labels_override = [];



    /**
     * Activate 'single term' mode on this taxonomy
     *
     * @var bool
     * @since 1.3
     */
    public static $single_term = false;


    /**
     * 'single term' mode params
     *
     * @var array
     * @since 1.3
     */
    public static $single_term_params = [];


    /**
     * Default 'single term' mode params
     *
     * @var array
     * @since 1.3
     */
    private static $_default_single_term_params = [
        'default_term'          => null,      // Term name, slug or id
        'priority'              => 'default',     // 'high', 'core', 'default' or 'low'
        'context'               => 'side',  // 'normal', 'advanced', or 'side'
        'force_selection'       => true,      // Set to true to hide "None" option & force a term selection
        'children_indented'     => false,
        'allow_new_terms'       => false,
    ];

    /********************************/
    /*                              */
    /*           Methods            */
    /*                              */
    /********************************/



    /**
     * Prevent class initialisation thru 'new Class'
     *
     * @access private
     * @since 1.3
     */
    private function __construct()
    {
    }


    /**
     * Taxonomy init (registration)
     *
     * @return void
     * @since 1.3
     */
    public static function init()
    {
        static::register(static::$args_override, static::$labels_override);

        if (static::$single_term) {
            static::setupSingleTerm();
        }
    }


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
     *
     * @since 1.2
     */
    protected static function graphqlFormatName(string $type): string
    {
        return lcfirst(preg_replace('/\s/', '', ucwords(str_replace('-', ' ', sanitize_title($type)))));
    }


    /**
     * Set this Taxonomy as as Single Term taxonomy
     *
     * @return void
     * @since 1.3
     */
    public static function setupSingleTerm(): void
    {
        $available_properties = [
            'default' => 'default_term',
            'priority',
            'context',
            'force_selection',
            'indented' => 'children_indented',
            'allow_new_terms',
        ];

        $params = static::$single_term_params + self::$_default_single_term_params;

        $taxonomy_box = new TaxonomySingleTerm(static::$taxonomy);

        $taxonomy_box->set('metabox_title', __(static::$singular, static::$domain));

        foreach ($available_properties as $tst_property => $op_property) {
            if (!isset($params[$op_property]) || $params[$op_property] == null) {
                continue;
            }

            $key = is_string($tst_property) ? $tst_property : $op_property;

            $taxonomy_box->set($key, $params[$op_property]);
        }
    }
}
