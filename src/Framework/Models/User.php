<?php

namespace OP\Framework\Models;

use AmphiBee\Eloquent\Model\User as UserModel;

/**
 * The user model.
 *
 * @package  ObjectPress
 * @author   tgeorgel <thomas@hydrat.agency>
 * @access   public
 * @since    2.1
 */
class User extends UserModel
{
    /**
     * Get the user roles
     *
     * @return array
     * @since 1.0.0
     */
    public function roles()
    {
        return get_user_by('id', $this->id)->roles;
    }

    /**
     * Get the user roles
     *
     * @return array
     * @since 1.0.0
     */
    public function caps()
    {
        return get_user_by('id', $this->id)->allcaps;
    }

    /**
     * Check if user has any of the specified role(s)
     *
     * @param string|array $roles Role or array of roles to be checked
     *
     * @return bool
     * @since 1.0.0
     */
    public function hasRole($roles)
    {
        $u_roles = $this->roles();
        $roles = is_array($roles) ? $roles : [$roles];

        foreach ($roles as $role) {
            if (in_array($role, $u_roles)) {
                return true;
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
        $user = get_user_by('id', $this->id);

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
        $user = get_user_by('id', $this->id);

        if (is_a($user, 'WP_User')) {
            $user->remove_role($role);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user has the specified capability
     *
     * @param string|array $caps Capability or array of capabilities to be checked
     *
     * @return bool
     * @since 1.0.0
     */
    public function can($caps)
    {
        if (is_string($caps)) {
            return user_can($this->id, $caps);
        }
        if (is_array($caps)) {
            foreach ($caps as $cap) {
                if (!user_can($this->id, $cap)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get a user depending on an attribute.
     *
     * @param   string $attribute Can be one of ID | slug | email | login
     * @param   mixed  $value     Value of the attribute
     *
     * @return  User|false on failure.
     * @since 1.0.0
     *
     * @reference https://developer.wordpress.org/reference/functions/get_user_by/
     */
    public static function findBy(string $attribute, $value)
    {
        $user = \get_user_by($attribute, $value);

        return ($user !== false) ? static::find($user->data->ID) : false;
    }
}
