<?php

namespace BlackrockWeb\ObjectPress\Models;

use stdClass;
use App\Models\Search;

class UserModel
{
    /**
     * User_id
     */
    protected $user_id;

    /**
     * User object
     */
    protected $user;


    /**
     * User class constructor setup variables
     *
     * @param int $user_id
     */
    public function __construct(int $user_id)
    {
        $this->user = get_userdata($user_id);
        $this->user_id = $this->user->ID;
    }



    /// *****  Class functions  ***** ///



    /**
     * Retreive user_id
     *
     * @return int
     */
    public function id()
    {
        return $this->user_id;
    }


    /**
     * Get the user metas
     *
     * @return array
     */
    public function metas()
    {
        return $this->getMeta();
    }


    /**
     * Get the user metas
     *
     * @return array
     */
    public function metasKeys()
    {
        return array_keys($this->getMeta());
    }


    /**
     * Get the user metas
     *
     * @return array
     */
    public function roles()
    {
        return $this->user->roles;
    }



    /** Getters **/



    /**
     * Retreive all metas or a specific meta identified by a meta_key
     *
     * @param string $meta_key (optionnal) meta_key to find
     * @param bool   $single   (optionnal) should it get all the fields or single one
     *
     * @return mixed
     *
     * @reference https://developer.wordpress.org/reference/functions/get_user_meta/
     */
    public function getMeta(string $meta_key = '', bool $single = false)
    {
        return get_user_meta($this->user_id, $meta_key, $single);
    }



    /** Checkers **/



    /**
     * Check if user has specified role(s)
     *
     * @param string|array $roles Role or array of roles to be checked
     *
     * @return bool
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



    /** Static methods **/



    /**
     * Get current loggedin user's class object
     *
     * @return int 0 on not found
     */
    public static function current()
    {
        return new static(get_current_user_id());
    }
}
