<?php

namespace OP\Framework\Models;

// require_once __DIR__ . '/../../../../../../wp/wp-includes/pluggable.php';

use \WP_User;

// TODO: Set up attributes system as posts


/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.3.1
 * @access   public
 * @since    7.0
 */
abstract class User
{
    /**
     * @var int
     * @since 1.0.0
     */
    protected $user_id;


    /**
     * @var \WP_User
     * @since 1.0.0
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
     * @since 1.0.0
     */
    public function get(bool $refresh = false)
    {
        if (!isset($this->user) || $refresh === true) {
            $this->user = get_userdata($this->user_id);

            if ($this->user === false) {
                return false;
            }

            $this->user_id = $this->user->ID;
        }

        return $this->user;
    }


    /**
     * Retreive user_id
     *
     * @return int
     * @since 1.0.0
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
     * @since 1.0.0
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
     * @since 1.0.0
     */
    public function metas()
    {
        return $this->getMeta();
    }


    /**
     * Get the user metas as key only
     *
     * @return array
     * @since 1.0.0
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
     * @since 1.0.0
     *
     * @reference https://developer.wordpress.org/reference/functions/get_user_meta/
     */
    public function getMeta(string $meta_key = '', bool $single = false)
    {
        return get_user_meta($this->user_id, $meta_key, $single);
    }


    /**
     * Set or update a meta identified by a meta_key
     *
     * @param string $key       Meta key to update
     * @param mixed  $value     Meta value to set
     * @param mixed  $multiple  Tells if meta key is unique or not
     *
     * @return mixed
     * @since 1.0.1
     *
     * @reference https://developer.wordpress.org/reference/functions/update_user_meta/
     */
    public function setMeta(string $key, $value, $multiple = false)
    {
        if ($multiple === false) {
            $result = update_user_meta($this->user_id, $key, $value);

            if (is_a($result, 'WP_Error')) {
                throw new \Exception("ObjectPress: update_user_meta() returned a \WP_Error");
            }
        } else {
            $result = add_user_meta($this->user_id, $key, $value, false);
            if (is_a($result, 'WP_Error')) {
                throw new \Exception("ObjectPress: add_user_meta() returned a \WP_Error");
            }
        }

        return $result;
    }


    /**
     * Update user metas
     *
     * @param array $metas ['meta_key' => 'meta_value']
     *
     * @return void
     * @since 1.0.1
     */
    public function setMetas(array $metas)
    {
        foreach ($metas as $key => $value) {
            $this->setMeta($key, $value);
        }
    }



    /******************************************/
    /*                                        */
    /*           Security & access            */
    /*                                        */
    /******************************************/


    /**
     * Get a new password reset token for the user
     *
     * @return string
     * @since 1.0.1
     * @reference https://developer.wordpress.org/reference/functions/get_password_reset_key/
     */
    public function getPasswordResetKey()
    {
        return get_password_reset_key($this->user);
    }


    /**
     * Get a new password reset token for the user
     *
     * @return string
     * @since 1.0.1
     * @reference https://developer.wordpress.org/reference/functions/check_password_reset_key/
     */
    public function checkPasswordResetKey(string $token)
    {
        return check_password_reset_key($token, $this->get()->user_login);
    }


    /**
     * Set a new encrypted password for the user
     *
     * @param string $password Plain text password
     *
     * @return string
     * @since 1.0.1
     * @reference https://developer.wordpress.org/reference/functions/wp_set_password/
     */
    public function setPassword(string $password)
    {
        return wp_set_password($password, $this->user_id);
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
     * @since 1.0.0
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
     * @since 1.0.0
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
     * @since 1.0.0
     */
    public function addRole(string $role)
    {
        $user = get_user_by('id', $this->user_id);

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
     * @since 1.0.0
     */
    public function removeRole($role)
    {
        $user = get_user_by('id', $this->user_id);

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
     * @since 1.0.0
     */
    public static function current()
    {
        $current = get_current_user_id();

        if ($current) {
            return new static($current);
        }

        return false;
    }


    /**
     * Get a user depending on an attribute
     * Return false if not found
     *
     * @param   string $attribute Can be one of ID | slug | email | login
     * @param   mixed  $value     Value of the attribute
     *
     * @return  User|false
     * @since 1.0.0
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
     * @since 1.0.1
     * @reference https://developer.wordpress.org/reference/functions/wp_create_user/
     */
    public static function create(string $username, string $email, string $password = '')
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


    /**
     * Create a new user using wp_insert_user() and returns new self.
     * If no password is provided, creates a 25 char lenght secure password
     *
     * @param array $userData User data to insert
     *
     * @return self|false on failure
     * @since 1.0.1
     * @reference https://developer.wordpress.org/reference/functions/wp_insert_user/
     */
    public static function insert(array $userData)
    {
        if (!isset($userData['user_pass'])) {
            $userData['user_pass'] = wp_generate_password(25, true);
        }

        $user_id = wp_insert_user($userData);

        if (is_a($user_id, 'WP_Error')) {
            return $user_id;
        }

        return new static($user_id);
    }
}
