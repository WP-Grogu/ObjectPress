<?php

namespace OP\Framework\Boilerplates;

use OP\Support\Facades\Locale;
use OP\Framework\Boilerplates\Traits\Common;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.3
 * @access   public
 * @since    1.0.0
 */
abstract class CustomPostType
{
    use Common;

    /********************************/
    /*                              */
    /*        Default params        */
    /*                              */
    /********************************/


    /**
     * Custom post type name/key
     * @var string
     * @since 1.0.0
     */
    protected static $cpt = 'custom-post-type';


    /**
     * Singular and plural names of CPT
     *
     * @var string
     * @since 1.0.0
     */
    public static $singular = 'Custom post type';
    public static $plural   = 'Custom post types';


    /**
     * Menu icon to display in back-office (dash-icon)
     *
     * @var string
     * @since 1.0.3
     */
    public static $menu_icon = 'dashicons-book';



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
     * Class constructor, register CTP to wordpress
     *
     * @param array $args   Optionnal. Args to overide.
     * @param array $labels Optionnal. Labels to override.
     *
     * @return void
     * @version 1.0.3
     * @since 1.0
     */
    protected static function register()
    {
        if (post_type_exists(static::$cpt)) {
            return;
        }

        $base_labels = self::generateLabels();
        $labels      = array_replace($base_labels, static::$labels_override);

        $base_args   = self::generateArgs($labels);
        $args        = array_replace($base_args, static::$args_override);

        register_post_type(static::$cpt, $args);
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

        $i18n_labels = Locale::getDomain('labels.cpt', static::$i18n_base_lang);

        return [
            'name'                  => _x(sprintf($i18n_labels['name'], $plural), 'Post Type General Name', $domain),
            'singular_name'         => _x(sprintf($i18n_labels['singular_name'], $singular), 'Post Type Singular Name', $domain),
            'menu_name'             => __(sprintf($i18n_labels['menu_name'], $plural), $domain),
            'name_admin_bar'        => __(sprintf($i18n_labels['name_admin_bar'], $singular), $domain),
            'archives'              => __(sprintf($i18n_labels['archives'], $singular), $domain),
            'attributes'            => __(sprintf($i18n_labels['attributes'], $singular), $domain),
            'parent_item_colon'     => __(sprintf($i18n_labels['parent_item_colon'], $singular), $domain),
            'all_items'             => __(sprintf($i18n_labels['all_items'], $plural), $domain),
            'add_new_item'          => __(sprintf($i18n_labels['add_new_item'], $singular), $domain),
            'add_new'               => __($i18n_labels['add_new'], $domain),
            'new_item'              => __(sprintf($i18n_labels['new_item'], $singular), $domain),
            'edit_item'             => __(sprintf($i18n_labels['edit_item'], $singular), $domain),
            'update_item'           => __(sprintf($i18n_labels['update_item'], $singular), $domain),
            'view_item'             => __(sprintf($i18n_labels['view_item'], $singular), $domain),
            'view_items'            => __(sprintf($i18n_labels['view_items'], $plural), $domain),
            'search_items'          => __(sprintf($i18n_labels['search_items'], $singular), $domain),
            'not_found'             => __($i18n_labels['not_found'], $domain),
            'not_found_in_trash'    => __($i18n_labels['not_found_in_trash'], $domain),
            'featured_image'        => __($i18n_labels['featured_image'], $domain),
            'set_featured_image'    => __($i18n_labels['set_featured_image'], $domain),
            'remove_featured_image' => __($i18n_labels['remove_featured_image'], $domain),
            'use_featured_image'    => __($i18n_labels['use_featured_image'], $domain),
            'insert_into_item'      => __(sprintf($i18n_labels['insert_into_item'], $singular), $domain),
            'uploaded_to_this_item' => __(sprintf($i18n_labels['uploaded_to_this_item'], $singular), $domain),
            'items_list'            => __(sprintf($i18n_labels['items_list'], $plural), $domain),
            'items_list_navigation' => __(sprintf($i18n_labels['items_list_navigation'], $plural), $domain),
            'filter_items_list'     => __(sprintf($i18n_labels['filter_items_list'], $plural), $domain),
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
        $domain     = static::getDomain();

        $i18n_labels = Locale::getDomain('labels.cpt', static::$i18n_base_lang);

        $pronouns = [
            'male'   => Locale::get('words.male_pronoun', static::$i18n_base_lang),
            'female' => Locale::get('words.female_pronoun', static::$i18n_base_lang),
        ];

        $genre = static::$i18n_is_female ? 'female' : 'male';

        $args = [
            'label'                 => __(sprintf($i18n_labels['label'], $singular), $domain),
            'description'           => __(sprintf($i18n_labels['description'], $pronouns[$genre], $singular), $domain),
            'labels'                => $labels,
            'supports'              => ['title', 'thumbnail'],
            'taxonomies'            => [],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => static::$menu_icon,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => false,
            'rest_base'             => static::$cpt,
        ];

        // If graphql is enabled, we need to setup some more params
        if (static::$graphql_enabled) {
            $args = array_replace($args, [
                'show_in_graphql'       => true,
                'graphql_single_name'   => static::graphqlFormatName($singular),
                'graphql_plural_name'   => static::graphqlFormatName($plural),
                'graphql_singular_type' => static::graphqlFormatName($singular),
                'graphql_plural_type'   => static::graphqlFormatName($plural),
            ]);
        }

        return $args;
    }


    /**
     * Convert CPT names to graphql format
     * eg: 'Étude de cas' => 'etudeDeCas'
     *
     * @param string
     * @return string
     * @since 1.0.0
     */
    protected static function graphqlFormatName(string $type)
    {
        return lcfirst(preg_replace('/\s/', '', ucwords(str_replace('-', ' ', sanitize_title($type)))));
    }



    /********************************/
    /*                              */
    /*       Public Methods         */
    /*                              */
    /********************************/



    /**
     * Custom post type init (registration)
     *
     * @return void
     * @since 1.0.3
     */
    public static function init()
    {
        if (!static::$i18n_domain) {
            static::$i18n_domain = defined('OP_DEFAULT_I18N_DOMAIN_CPTS') ? OP_DEFAULT_I18N_DOMAIN_CPTS : 'op-theme-cpts';
        }

        static::register();
    }
}
