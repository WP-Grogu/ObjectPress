<?php

namespace OP\Framework\Models\Traits;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.1
 * @access   public
 * @since    0.1
 */
trait PostType
{
    /**
     * Register model's post_type
     *
     * @access private
     * @return void
     * @since 0.1
     */
    public function pTypeExistsOrFail()
    {
        if ((!isset(static::$post_type)
            || empty(static::$post_type)
            || !post_type_exists(static::$post_type))
            && static::$post_type !== 'post'
            && static::$post_type !== 'page'
        ) {
            throw new \Exception((static::$post_type ?? 'undefined') . " is not a registred post_type");
        }
    }


    /**
     * Return post_type
     *
     * @return string post_type
     * @since 0.1
     */
    public function postType()
    {
        return static::$post_type;
    }


    /**
     * Return Custom post type label
     * if no params are specified, get the CPT singular name
     *
     * @param string $x Label tag to retrieve
     *
     * @return string|void
     * @since 0.1
     */
    public function cptLabel(string $x = 'singular_name')
    {
        $labels = $this->cptLabels();

        if ($labels) {
            $labels = (array) $labels;
            return $labels[$x] ?? '';
        }
    }


    /**
     * Retrieves post type labels.
     *
     * @return object|null
     * @since 0.1
     */
    public function cptLabels()
    {
        $postType = get_post_type_object(static::$post_type);

        if ($postType) {
            return $postType->labels ?? [];
        }
    }


    /**
     * Retrieves a post type object by name.
     *
     * @return WP_Post_Type|null
     * @since 0.1
     */
    public function getPostTypeObject()
    {
        return get_post_type_object(static::$post_type);
    }
}
