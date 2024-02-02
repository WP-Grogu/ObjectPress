<?php

namespace OP\Framework\Wordpress;

use InvalidArgumentException;
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
     * The role options.
     */
    protected array $options = [];

    /**
     * The role capabilities.
     */
    protected array $caps = [];

    /**
     * The role default options, merged with options.
     */
    protected array $defaults = [
        'name'       => '',
        'label'      => '',
        'extends'    => false,
        'backoffice' => false,
    ];

    /**
     * The computed role options.
     *
     * @var object
     */
    private $conf;

    /**
     * The admin menu items the current role SHOULD NOT see.
     *
     * @var array
     * @since 1.0.4
     */
    protected array $hidden_menues = [];

    /**
     * Should translate the label.
     *
     * @since 2.5.0
     */
    protected bool $translate_labels = false;

    /**
     * i18n translation domain.
     *
     * @var string
     * @since 1.0.4
     */
    protected string $i18n_domain = '';

    /**
     * i18n cpt default lang (format: 'en', 'fr'..).
     * Leave empty string to use the app default lang instead.
     * App default lang is defined by it's dedicated constant, default WPML/PolyLang lang, or wordpress locale.
     *
     * @var string
     * @since 1.0.4
     */
    protected string $i18n_base_lang = '';

    /**
     * Role constructor. Ensure mandatory options are set.
     *
     * @return void
     * @since 2.0
     */
    public function __construct()
    {
        $this->conf = (object) ($this->options + $this->defaults);

        if (! $this->conf->label || ! $this->conf->name) {
            throw new InvalidArgumentException("ObjectPress : The `label` and `name` options are mandatory.");
        }
    }


    /**
     * Generate the caps based on the extended roles + custom perms.
     *
     * @return array
     * @since 1.0.4
     */
    public function generateCaps()
    {
        $caps = [];

        $caps = $caps + ['read' => ((bool) ($this->conf->backoffice))];

        if ($this->conf->extends && is_string($this->conf->extends)) {
            $extends = get_role($this->conf->extends);

            if (!$extends) {
                throw new RoleNotFoundException(
                    sprintf("ObjectPress : The extended role `%s` was not found.", $this->conf->extends)
                );
            }

            $caps = $caps + $extends->capabilities;
        }

        return $this->caps + $caps;
    }


    /**
     * Register Role to wordpress
     *
     * @return void
     * @version 1.0.4
     * @since 1.0.4
     */
    protected function register()
    {
        $caps = $this->generateCaps();
        $label = $this->translate_labels ? __($this->conf->label, $this->i18n_domain) : $this->conf->label;
        $role = get_role($this->conf->name);

        if (!$role) {
            add_role($this->conf->name, $label, $caps);
        } else {
            if ($role->capabilities !== $caps) {
                foreach ($role->capabilities as $name => $value) {
                    $role->remove_cap($name);
                }
                foreach ($caps as $name => $value) {
                    $role->add_cap($name, $value);
                }
            }
        }

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
        $role  = $this->conf->name;

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

    public function removeCaps(string | array $caps)
    {
        if (!($role = get_role($this->conf->name))) {
            throw new RoleNotFoundException(
                sprintf("ObjectPress : The role `%s` was not found.", $this->conf->name)
            );
        }

        foreach ((array) $caps as $cap) {
            $role->remove_cap($cap);
        }
    }

    public function addCaps(array $caps)
    {
        if (!($role = get_role($this->conf->name))) {
            throw new RoleNotFoundException(
                sprintf("ObjectPress : The role `%s` was not found.", $this->conf->name)
            );
        }

        foreach ($caps as $name => $value) {
            $role->add_cap($name, $value);
        }
    }

    public function replaceCaps(array $caps)
    {
        if (!($role = get_role($this->conf->name))) {
            throw new RoleNotFoundException(
                sprintf("ObjectPress : The role `%s` was not found.", $this->conf->name)
            );
        }

        foreach ($role->capabilities as $name => $value) {
            $role->remove_cap($name);
        }

        foreach ($caps as $name => $value) {
            $role->add_cap($name, $value);
        }
    }

    public function getName()
    {
        return $this->conf->name;
    }

    public function getLabel()
    {
        return $this->conf->label;
    }
}
