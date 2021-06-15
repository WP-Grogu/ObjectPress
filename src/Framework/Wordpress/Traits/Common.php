<?php

namespace OP\Framework\Wordpress\Traits;

use OP\Core\Locale;
use OP\Support\Facades\Config;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.3
 * @access   public
 * @since    1.0.3
 */
trait Common
{
    /**
     * CPT/Taxonomy argument to overide over boilerplate
     *
     * @var array
     * @since 1.0.3
     */
    public $args_override = [];


    /**
     * CPT/Taxonomy labels to overide over boilerplate
     *
     * @var array
     * @since 1.0.3
     */
    public $labels_override = [];


    /**
     * Enable graphql on this CPT/Taxonomy
     *
     * @var bool
     * @since 1.0.0
     */
    public $graphql_enabled = false;


    /**
     * i18n translation domain
     *
     * @var string
     * @since 1.0.0
     */
    protected $i18n_domain = '';


    /**
     * i18n cpt default lang (format: 'en', 'fr'..).
     * Leave empty string to use the app default lang instead.
     * App default lang is defined by it's dedicated constant, default WPML/PolyLang lang, or wordpress locale.
     *
     * @var string
     * @since 1.0.3
     */
    protected $i18n_base_lang = '';


    /**
     * Used to display male/female pronoun on concerned languages
     * Set true if should use female pronoun for this cpt
     *
     * @var bool
     * @since 1.0
     */
    public $i18n_is_female = false;




    /********************************/
    /*                              */
    /*       Public Methods         */
    /*                              */
    /********************************/



    /**
     * Get the custom post type or taxonomy name (wp identifier)
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getName();
    }


    /**
     * Get the custom post type or taxonomy name (wp identifier)
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Returns Taxonomy's domain for string translation
     *
     * @return string
     * @since 1.0.0
     */
    public function getDomain()
    {
        $domain = $this->i18n_domain;

        if (Config::get('i18n.suffix_domains_current_lang') === true) {
            $domain .= '__' . ($this->i18n_base_lang ?: Locale::defaultLang());
        }

        return $domain;
    }


    /**
     * Convert CPT / Taxonomy names to graphql format.
     * eg: 'Étude de cas' => 'etudeDeCas'
     *
     * @param string
     * @return string
     *
     * @since 1.2
     */
    protected function graphqlFormatName(string $type): string
    {
        return lcfirst(
            preg_replace(
                '/\s/',
                '',
                ucwords(
                    str_replace(
                        ['—', '-', '/', '\\'],
                        ' ',
                        sanitize_title($type)
                    )
                )
            )
        );
    }

    
    /**
     * The post type singular name, camel case.
     *
     * @return string
     */
    public function getCamelizedSingular()
    {
        return $this->graphqlFormatName($this->singular);
    }


    /**
     * The post type plural name, camel case.
     *
     * @return string
     */
    public function getCamelizedPlural()
    {
        return $this->graphqlFormatName($this->plural);
    }


    /**
     * Make sure the required properties are defined
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateProperties()
    {
        $mandatory = [
            'name',
            'singular',
            'plural',
        ];

        foreach ($mandatory as $property) {
            if (!$this->{$property}) {
                throw new \InvalidArgumentException(
                    sprintf('The `%s` property is mandatory on class `%s`.', $property, )
                );
            }
        }
    }

    /**
     * @access public
     * @since 1.0.3
     * @version 2.0
     */
    public function __construct()
    {
        $this->validateProperties();
    }
}
