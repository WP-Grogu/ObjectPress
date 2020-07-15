<?php

namespace OP\Framework\Boilerplates;

use OP\Framework\Boilerplates\Traits\Common;
use OP\Support\Facades\Locale;
use OP\Lib\TaxonomySingleTerm\TaxonomySingleTerm;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.3
 * @access   public
 * @since    1.0.0
 */
abstract class Taxonomy
{
    use Common;

    /********************************/
    /*                              */
    /*        Default params        */
    /*                              */
    /********************************/


    /**
     * Taxonomy name
     *
     * @var string
     * @since 1.0.0
     */
    protected static $taxonomy = 'custom-taxonomy';


    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     * @since 1.0.0
     */
    public static $singular = 'Custom Taxonomy';
    public static $plural   = 'Custom Taxonomies';


    /**
     * Register this taxonomy on thoses post types
     *
     * @var array
     * @since 1.0.0
     */
    protected static $post_types = [];



    /**
     * Activate 'single term' mode on this taxonomy
     *
     * @var bool
     * @since 1.0.3
     */
    public static $single_term = false;


    /**
     * Single term box type ('select' or 'radio', default to radio)
     *
     * @var string
     * @since 1.0.4
     */
    public static $single_term_type = 'radio';


    /**
     * 'single term' mode params
     *
     * @var array
     * @since 1.0.3
     */
    public static $single_term_params = [];


    /**
     * Default 'single term' mode params
     *
     * @var array
     * @since 1.0.3
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
    /*       Private Methods        */
    /*                              */
    /********************************/



    /**
     * Prevent class initialisation thru 'new Class'
     *
     * @access private
     * @since 1.0.3
     */
    private function __construct()
    {
    }


    /**
     * Class constructor, register Taxonomy to wordpress
     *
     * @param array $args   Optionnal. Args to overide.
     * @param array $labels Optionnal. Labels to override.
     *
     * @return void
     * @since 1.0.0
     */
    protected static function register()
    {
        if (taxonomy_exists(static::$taxonomy)) {
            return;
        }

        $base_labels = self::generateLabels();
        $labels      = array_replace($base_labels, static::$labels_override);

        $base_args   = self::generateArgs($labels);
        $args        = array_replace($base_args, static::$args_override);

        $post_types = array_map('strtolower', static::$post_types);

        register_taxonomy(static::$taxonomy, $post_types, $args);
    }

    /**
     * Generate the labels based on i18n default lang
     *
     * @return array
     */
    private static function generateLabels()
    {
        $singular   = static::$singular;
        $plural     = static::$plural;
        $domain     = static::getDomain();

        $i18n_labels = Locale::getDomain('labels.taxo', static::$i18n_base_lang);

        return [
            'name'              => _x(sprintf($i18n_labels['name'], $plural), 'taxonomy general name', $domain),
            'singular_name'     => _x(sprintf($i18n_labels['singular_name'], $singular), 'taxonomy singular name', $domain),
            'search_items'      => __(sprintf($i18n_labels['search_items'], $plural), $domain),
            'all_items'         => __(sprintf($i18n_labels['all_items'], $plural), $domain),
            'parent_item'       => __(sprintf($i18n_labels['parent_item'], $singular), $domain),
            'parent_item_colon' => __(sprintf($i18n_labels['parent_item_colon'], $singular), $domain),
            'edit_item'         => __(sprintf($i18n_labels['edit_item'], $singular), $domain),
            'update_item'       => __(sprintf($i18n_labels['update_item'], $singular), $domain),
            'add_new_item'      => __(sprintf($i18n_labels['add_new_item'], $singular), $domain),
            'new_item_name'     => __(sprintf($i18n_labels['new_item_name'], $singular), $domain),
            'menu_name'         => __(sprintf($i18n_labels['menu_name'], $plural), $domain),
        ];
    }


    /**
     * Generate the args based on i18n default lang
     *
     * @param array $labels
     *
     * @return array
     */
    private static function generateArgs(array $labels)
    {
        $singular   = static::$singular;
        $plural     = static::$plural;

        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
        ];

        // If graphql is enabled, we need to setup some more params
        if (static::$graphql_enabled) {
            $args = array_replace($args, [
                'show_in_graphql'       => true,
                'graphql_single_name'   => static::graphqlFormatName($singular),
                'graphql_plural_name'   => static::graphqlFormatName($plural),
            ]);
        }

        return $args;
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
     * @since 1.0.3
     */
    protected static function setupSingleTerm(): void
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

        $taxonomy_box = new TaxonomySingleTerm(static::$taxonomy, static::$post_types, static::$single_term_type);

        $taxonomy_box->set('metabox_title', __(static::$singular, static::getDomain()));

        foreach ($available_properties as $tst_property => $op_property) {
            if (!isset($params[$op_property]) || $params[$op_property] == null) {
                continue;
            }

            $key = is_string($tst_property) ? $tst_property : $op_property;

            $taxonomy_box->set($key, $params[$op_property]);
        }
    }



    /********************************/
    /*                              */
    /*       Public Methods         */
    /*                              */
    /********************************/



    /**
     * Taxonomy init (registration)
     *
     * @return void
     * @since 1.0.3
     */
    public static function init()
    {
        if (!static::$i18n_domain) {
            static::$i18n_domain = defined('OP_DEFAULT_I18N_DOMAIN_TAXOS') ? OP_DEFAULT_I18N_DOMAIN_TAXOS : 'op-theme-taxos';
        }

        static::register();

        if (static::$single_term) {
            static::setupSingleTerm();
        }
    }
}
