<?php

namespace OP\Framework\Boilerplates;

use Exception;
use OP\Support\Facades\Locale;
use OP\Framework\Boilerplates\Traits\Common;
use OP\Framework\Exceptions\RoleNotFoundException;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.4
 * @access   public
 * @since    1.0.4
 */
abstract class Role
{
    /**
     * The role to extend caps from.
     *
     * @var string
     * @since 1.0.4
     */
    protected static $extends = '';


    /**
     * i18n translation domain.
     *
     * @var string
     * @since 1.0.4
     */
    protected static $i18n_domain = '';


    /**
     * i18n cpt default lang (format: 'en', 'fr'..).
     * Leave empty string to use the app default lang instead.
     * App default lang is defined by it's dedicated constant, default WPML/PolyLang lang, or wordpress locale.
     *
     * @var string
     * @since 1.0.4
     */
    protected static $i18n_base_lang = '';


    /**
     * Base permissions needed to access back-office
     *
     * @var array
     * @since 1.0.4
     */
    private static $backoffice_caps = [
        'read' => true,
    ];



    /**
     * Generate the caps based on the extended roles + custom perms.
     *
     * @return array
     */
    public static function generateCaps()
    {
        $caps = [];

        if (static::$access_bo) {
            $caps = $caps + self::$backoffice_caps;
        }

        if (static::$extends) {
            $extends = get_role(static::$extends);
            
            if (!$extends) {
                throw new RoleNotFoundException(
                    sprintf("OP :: Role :: The extended role `%s` was not found.", static::$extends)
                );
            }

            $caps = $caps + $extends->capabilities;
        }

        return static::$caps + $caps;
    }


    /**
     * Class constructor, register Role to wordpress
     *
     * @return void
     * @version 1.0.4
     * @since 1.0.4
     */
    protected static function register()
    {
        $caps = static::generateCaps();

        if (get_role(static::$identifier)) {
            remove_role(static::$identifier);
        }

        add_role(static::$identifier, __(static::$name, static::$i18n_domain), $caps);
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
            static::$i18n_domain = defined('OP_DEFAULT_I18N_DOMAIN_ROLES') ? OP_DEFAULT_I18N_DOMAIN_ROLES : 'op-theme-roles';
        }

        static::register();
    }


    public static function addCaps()
    {
        // TODO
    }

    public static function getName()
    {
        return static::$name;
    }
}
