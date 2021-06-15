<?php

namespace OP\Framework\Wordpress;

use OP\Support\Facades\Locale;
use OP\Framework\Wordpress\Traits\Common;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    1.0.0
 */
abstract class PostType
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
    protected $name = '';


    /**
     * Singular and plural names of CPT
     *
     * @var string
     * @since 1.0.0
     */
    public $singular = 'Custom post type';
    public $plural   = 'Custom post types';


    /**
     * Menu icon to display in back-office (dash-icon)
     *
     * @var string
     * @since 1.0.3
     */
    public $menu_icon = 'dashicons-book';



    /********************************/
    /*                              */
    /*       Private Methods        */
    /*                              */
    /********************************/


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
    protected function register()
    {
        if (post_type_exists($this->name)) {
            return;
        }

        $base_labels = $this->generateLabels();
        $labels      = array_replace($base_labels, $this->labels_override);

        $base_args   = $this->generateArgs($labels);
        $args        = array_replace($base_args, $this->args_override);

        register_post_type($this->name, $args);
    }


    /**
     * Generate the labels based on i18n default lang
     *
     * @return array
     */
    private function generateLabels()
    {
        $singular   = $this->singular;
        $plural     = $this->plural;
        $domain     = $this->getDomain();

        $i18n_labels = Locale::getDomain('labels.cpt', $this->i18n_base_lang);

        $genre = $this->i18n_is_female ? 'female' : 'male';
        $words = [
            'all' => Locale::get("words.{$genre}_all", $this->i18n_base_lang),
            'new' => Locale::get("words.{$genre}_new", $this->i18n_base_lang),
        ];

        return [
            'name'                  => _x(sprintf($i18n_labels['name'], $plural), 'Post Type General Name', $domain),
            'singular_name'         => _x(sprintf($i18n_labels['singular_name'], $singular), 'Post Type Singular Name', $domain),
            'menu_name'             => __(sprintf($i18n_labels['menu_name'], $plural), $domain),
            'name_admin_bar'        => __(sprintf($i18n_labels['name_admin_bar'], $singular), $domain),
            'archives'              => __(sprintf($i18n_labels['archives'], $singular), $domain),
            'attributes'            => __(sprintf($i18n_labels['attributes'], $singular), $domain),
            'parent_item_colon'     => __(sprintf($i18n_labels['parent_item_colon'], $singular), $domain),
            'all_items'             => __(sprintf($i18n_labels['all_items'], ucfirst($words['all']), $plural), $domain),
            'add_new_item'          => __(sprintf($i18n_labels['add_new_item'], $words['new'], $singular), $domain),
            'add_new'               => __($i18n_labels['add_new'], $domain),
            'new_item'              => __(sprintf($i18n_labels['new_item'], ucfirst($words['new']), $singular), $domain),
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
    private function generateArgs(array $labels)
    {
        $singular   = $this->singular;
        $plural     = $this->plural;
        $domain     = $this->getDomain();

        $i18n_labels = Locale::getDomain('labels.cpt', $this->i18n_base_lang);

        $pronouns = [
            'male'   => Locale::get('words.male_pronoun', $this->i18n_base_lang),
            'female' => Locale::get('words.female_pronoun', $this->i18n_base_lang),
        ];

        $genre = $this->i18n_is_female ? 'female' : 'male';

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
            'menu_icon'             => $this->menu_icon,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => false,
            'rest_base'             => $this->name,
        ];

        // If graphql is enabled, we need to setup some more params
        if ($this->graphql_enabled) {
            $args = array_replace($args, [
                'show_in_graphql'       => true,
                'graphql_single_name'   => $this->getCamelizedSingular(),
                'graphql_plural_name'   => $this->getCamelizedPlural(),
            ]);
        }

        return $args;
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
    public function boot()
    {
        if (!$this->i18n_domain) {
            $this->i18n_domain = defined('OP_DEFAULT_I18N_DOMAIN_CPTS') ? OP_DEFAULT_I18N_DOMAIN_CPTS : 'op-theme-cpts';
        }

        if (method_exists($this, 'conditionnalInitialization') && !call_user_func([$this, 'conditionnalInitialization'])) {
            return;
        }

        $this->register();
    }


    /**
     * Custom post type init (registration)
     *
     * @deprecated
     * @return void
     * @since 1.0.3
     */
    public function init()
    {
        $this->boot();
    }
}
