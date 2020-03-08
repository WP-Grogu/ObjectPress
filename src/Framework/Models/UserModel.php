<?php

namespace OP\Framework\Models;

// require_once __DIR__ . '/../../../../../../wp/wp-includes/pluggable.php';

use WP_User;

// TODO: Set up attributes system as posts


/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.1
 * @access   public
 * @since    7.0
 */
abstract class UserModel
{
    /**
     * @var int
     * @since 0.1
     */
    protected $user_id;


    /**
     * @var \WP_User
     * @since 0.1
     */
    private $user;


    /**
     * User class constructor setup variables
     *
     * @param int $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
        $this->get();
    }


    /**
     * Get the user data from WP
     *
     * @param bool $refresh If the user data should be refreshed from database
     *
     * @return object
     * @since 0.1
     */
    public function get(bool $refresh = false)
    {
        if (!isset($this->user) || $refresh === true) {
            $this->user = get_userdata($this->user_id);
            $this->user_id = $this->user->ID;
        }

        return $this->user;
    }


    /**
     * Retreive user_id
     *
     * @return int
     * @since 0.1
     */
    public function id()
    {
        return $this->user_id;
    }


    /**
     * Delete the User from database. If no $reassign user id specified,
     * destroy all user content
     *
     * @param int $reassign Reassign posts and links to new User ID.
     *
     * @return bool True when finished
     * @since 0.1
     */
    public function delete(int $reassign = null)
    {
        wp_delete_user($this->user_id, $reassign);
    }



    /******************************************/
    /*                                        */
    /*                 Metas                  */
    /*                                        */
    /******************************************/



    /**
     * Get the user metas
     *
     * @return array
     * @since 0.1
     */
    public function metas()
    {
        return $this->getMeta();
    }


    /**
     * Get the user metas as key only
     *
     * @return array
     * @since 0.1
     */
    public function metasKeys()
    {
        return array_keys($this->getMeta());
    }



    /**
     * Retreive all metas or a specific meta identified by a meta_key
     *
     * @param string $meta_key (optionnal) meta_key to find
     * @param bool   $single   (optionnal) should it get all the fields or single one
     *
     * @return mixed
     * @since 0.1
     *
     * @reference https://developer.wordpress.org/reference/functions/get_user_meta/
     */
    public function getMeta(string $meta_key = '', bool $single = false)
    {
        return get_user_meta($this->user_id, $meta_key, $single);
    }



    /******************************************/
    /*                                        */
    /*        Roles and permissions           */
    /*                                        */
    /******************************************/



    /**
     * Get the user roles
     *
     * @return array
     * @since 0.1
     */
    public function roles()
    {
        return $this->user->roles;
    }


    /**
     * Check if user has specified role(s)
     *
     * @param string|array $roles Role or array of roles to be checked
     *
     * @return bool
     * @since 0.1
     */
    public function hasRole($roles)
    {
        $u_roles = $this->roles();

        if (is_string($roles)) {
            return in_array($roles, $u_roles);
        }
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (in_array($role, $u_roles)) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Add a role to the current user
     *
     * @param  string role name
     *
     * @return void
     * @since 0.1
     */
    public function addRole(string $role)
    {
        $user = self::getBy('id', $this->user_id);

        if (is_a($user, 'WP_User')) {
            $user->add_role($role);
            return true;
        } else {
            return false;
        }
    }


    /**
     * Remove a role from the current user
     *
     * @param  string role name
     * @return void
     * @since 0.1
     */
    public function removeRole($role)
    {
        $user = self::getBy('id', $this->user_id);

        if (is_a($user, 'WP_User')) {
            $user->remove_role($role);
            return true;
        } else {
            return false;
        }
    }



    /******************************************/
    /*                                        */
    /*            Static Methods              */
    /*                                        */
    /******************************************/



    /**
     * Get current loggedin user's class object
     *
     * @return int 0 on not found
     * @since 0.1
     */
    public static function current()
    {
        return new static(get_current_user_id());
    }


    /**
     * Get a user depending on an attribute
     * Return false if not found
     *
     * @param   string $attribute Can be one of ID | slug | email | login
     * @param   mixed  $value     Value of the attribute
     *
     * @return  User|false
     * @since 0.1
     *
     * @reference https://developer.wordpress.org/reference/functions/get_user_by/
     */
    public static function getBy(string $attribute, $value)
    {
        $user = \get_user_by($attribute, $value);

        return ($user !== false) ? new static($user->data->ID) : false;
    }


    /**
     * Create a new user and returns new self.
     * If no password is provided, creates a 25 char lenght secure password
     *
     * @param string $username Username of the user to create
     * @param string $email Email of the user to create
     * @param string $password Password of the user to create (optionnal)
     *
     * @return self|false on failure
     * @since 0.1
     */
    public static function insert(string $username, string $email, string $password = '')
    {
        if (!$password) {
            $password = wp_generate_password(25, true);
        }

        $user_id = wp_create_user($username, $password, $email);

        if (is_a($user_id, 'WP_Error')) {
            return false;
        }

        return new static($user_id);
    }
}
