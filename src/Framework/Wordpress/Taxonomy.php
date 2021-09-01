<?php

namespace OP\Framework\Wordpress;

use OP\Framework\Wordpress\Traits\Common;
use OP\Support\Facades\Locale;
use OP\Lib\TaxonomySingleTerm\TaxonomySingleTerm;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
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
    protected $name = 'custom-taxonomy';


    /**
     * Singular and plural names of Taxonomy
     *
     * @var string
     * @since 1.0.0
     */
    public $singular = 'Custom Taxonomy';
    public $plural   = 'Custom Taxonomies';


    /**
     * Register this taxonomy on thoses post types
     *
     * @var array
     * @since 1.0.0
     */
    protected $post_types = [];



    /**
     * Activate 'single term' mode on this taxonomy
     *
     * @var bool
     * @since 1.0.3
     */
    public $single_term = false;


    /**
     * Single term box type ('select' or 'radio', default to radio)
     *
     * @var string
     * @since 1.0.4
     */
    public $single_term_type = 'radio';


    /**
     * 'single term' mode params
     *
     * @var array
     * @since 1.0.3
     */
    public $single_term_params = [];


    /**
     * Default 'single term' mode params
     *
     * @var array
     * @since 1.0.3
     */
    private $_default_single_term_params = [
        'default_term'      => null,        // Term name, slug or id
        'priority'          => 'default',   // 'high', 'core', 'default' or 'low'
        'context'           => 'side',      // 'normal', 'advanced', or 'side'
        'force_selection'   => true,        // Set to true to hide "None" option & force a term selection
        'children_indented' => false,
        'allow_new_terms'   => false,
    ];



    /********************************/
    /*                              */
    /*       Private Methods        */
    /*                              */
    /********************************/


    /**
     * Class constructor, register Taxonomy to wordpress
     *
     * @param array $args   Optionnal. Args to overide.
     * @param array $labels Optionnal. Labels to override.
     *
     * @return void
     * @since 1.0.0
     */
    protected function register()
    {
        if (taxonomy_exists($this->name)) {
            return;
        }

        $base_labels = $this->generateLabels();
        $labels      = array_replace($base_labels, $this->labels_override);

        $base_args   = $this->generateArgs($labels);
        $args        = array_replace($base_args, $this->args_override);

        $post_types = array_map('strtolower', $this->post_types);

        register_taxonomy($this->name, $post_types, $args);
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

        $i18n_labels = Locale::getDomain('labels.taxo', $this->i18n_base_lang);

        $genre = $this->i18n_is_female ? 'female' : 'male';
        $words = [
            'all' => Locale::get("words.{$genre}_all", $this->i18n_base_lang),
            'new' => Locale::get("words.{$genre}_new", $this->i18n_base_lang),
        ];

        return [
            'name'              => _x(sprintf($i18n_labels['name'], $plural), 'taxonomy general name', $domain),
            'singular_name'     => _x(sprintf($i18n_labels['singular_name'], $singular), 'taxonomy singular name', $domain),
            'search_items'      => __(sprintf($i18n_labels['search_items'], $plural), $domain),
            'all_items'         => __(sprintf($i18n_labels['all_items'], ucfirst($words['all']), $plural), $domain),
            'parent_item'       => __(sprintf($i18n_labels['parent_item'], $singular), $domain),
            'parent_item_colon' => __(sprintf($i18n_labels['parent_item_colon'], $singular), $domain),
            'edit_item'         => __(sprintf($i18n_labels['edit_item'], $singular), $domain),
            'update_item'       => __(sprintf($i18n_labels['update_item'], $singular), $domain),
            'add_new_item'      => __(sprintf($i18n_labels['add_new_item'], $words['new'], $singular), $domain),
            'new_item_name'     => __(sprintf($i18n_labels['new_item_name'], ucfirst($words['new']), $singular), $domain),
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
    private function generateArgs(array $labels)
    {
        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'query_var'         => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => true,
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


    /**
     * Set this Taxonomy as as Single Term taxonomy
     *
     * @return void
     * @since 1.0.3
     */
    protected function setupSingleTerm(): void
    {
        $available_properties = [
            'default' => 'default_term',
            'priority',
            'context',
            'force_selection',
            'indented' => 'children_indented',
            'allow_new_terms',
        ];

        $params = $this->single_term_params + $this->_default_single_term_params;

        $taxonomy_box = new TaxonomySingleTerm(
            $this->name,
            $this->post_types,
            $this->single_term_type
        );

        $taxonomy_box->set('metabox_title', __($this->singular, $this->getDomain()));

        foreach ($available_properties as $tst_property => $op_property) {
            if (!isset($params[$op_property]) || $params[$op_property] === null) {
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
    public function boot()
    {
        if (!$this->i18n_domain) {
            $this->i18n_domain = defined('OP_DEFAULT_I18N_DOMAIN_TAXOS') ? OP_DEFAULT_I18N_DOMAIN_TAXOS : 'op-theme-taxos';
        }

        if (method_exists($this, 'conditionnalInitialization') && !call_user_func([$this, 'conditionnalInitialization'])) {
            return;
        }

        $this->register();

        if ($this->single_term) {
            $this->setupSingleTerm();
        }
    }

    /**
     * Taxonomy init (registration)
     *
     * @deprecated
     * @return void
     * @since 1.0.3
     */
    public function init()
    {
        $this->boot();
    }


    /**
     * Get taxonomy name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Return taxonomy terms.
     *
     * @param bool|string (optionnal) If set, returns only the selected identifier (eg: 'slug', 'title') from WP_Term object
     * @param array       (optionnal) If set, add some rules to get_terms() $args parameter
     *
     * @return array
     */
    public function getTerms($identifier = false, array $args = [])
    {
        $args = $args + [
            'taxonomy'   => $this->name,
            'hide_empty' => false,
        ];

        $terms = \get_terms($args);

        if (!$identifier) {
            return $terms;
        }

        return array_filter(
            array_map(function ($e) use ($identifier) {
                return $e->{$identifier} ?? '';
            }, $terms)
        );
    }
}
