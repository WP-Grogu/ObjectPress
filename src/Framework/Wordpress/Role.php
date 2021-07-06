<?php

namespace OP\Framework\Wordpress;

use OP\Framework\Models\User;
use OP\Support\Facades\Theme;
use OP\Framework\Exceptions\RoleNotFoundException;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
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
    protected $extends = '';


    /**
     * i18n translation domain.
     *
     * @var string
     * @since 1.0.4
     */
    protected $i18n_domain = '';


    /**
     * The admin menu items the current role SHOULD NOT see.
     *
     * @var array
     * @since 1.0.4
     */
    protected $hidden_menues = [];


    /**
     * i18n cpt default lang (format: 'en', 'fr'..).
     * Leave empty string to use the app default lang instead.
     * App default lang is defined by it's dedicated constant, default WPML/PolyLang lang, or wordpress locale.
     *
     * @var string
     * @since 1.0.4
     */
    protected $i18n_base_lang = '';


    /**
     * Base permissions needed to access back-office
     *
     * @var array
     * @since 1.0.4
     */
    private $backoffice_caps = [
        'read' => true,
    ];



    /**
     * Generate the caps based on the extended roles + custom perms.
     *
     * @return array
     * @since 1.0.4
     */
    public function generateCaps()
    {
        $caps = [];

        if ($this->access_bo) {
            $caps = $caps + self::$backoffice_caps;
        }

        if ($this->extends) {
            $extends = get_role($this->extends);
            
            if (!$extends) {
                throw new RoleNotFoundException(
                    sprintf("ObjectPress : The extended role `%s` was not found.", $this->extends)
                );
            }

            $caps = $caps + $extends->capabilities;
        }

        return $this->caps + $caps;
    }


    /**
     * Class constructor, register Role to wordpress
     *
     * @return void
     * @version 1.0.4
     * @since 1.0.4
     */
    protected function register()
    {
        $caps = $this->generateCaps();

        if (get_role($this->identifier)) {
            remove_role($this->identifier);
        }

        add_role($this->identifier, __($this->name, $this->i18n_domain), $caps);

        $this->removeAdminMenuItems();
    }


    /**
     * Remove some menu items from admin menu for this specific role.
     *
     * @version 1.0.5
     * @since 1.0.4
     * @return void
     */
    protected function removeAdminMenuItems()
    {
        if (!is_array($this->hidden_menues) || empty($this->hidden_menues)) {
            return;
        }

        $slugs = $this->hidden_menues;
        $role  = $this->identifier;

        Theme::on('admin_menu', function () use ($slugs, $role) {
            $u = User::current();

            if ($u && $u->hasRole($role)) {
                foreach ($slugs as $slug) {
                    remove_menu_page($slug);
                }
            }
        }, 999);
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
            $this->i18n_domain = defined('OP_DEFAULT_I18N_DOMAIN_ROLES') ? OP_DEFAULT_I18N_DOMAIN_ROLES : 'op-theme-roles';
        }

        $this->register();
    }


    public function addCaps()
    {
        // TODO
    }

    public function getName()
    {
        return $this->name;
    }
}
