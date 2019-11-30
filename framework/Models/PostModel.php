<?php

namespace BlackrockWeb\ObjectPress\Models;

use Exception;
use stdClass;

class PostModel
{
    /**
     * Post_id
     */
    protected $post_id;


    /**
     * Register model's post type
     *
     * @return void
     */
    public function register()
    {
        if (
            !isset(static::$post_type)
            || empty(static::$post_type)
            || !post_type_exists(static::$post_type)
        ) {
            throw new Exception(static::$post_type ?? 'undefined' . " is not a registred post_type");
        }
    }


    /**
     * Model constructor
     * Retrieve post_type model from database and setup variables
     *
     * @param int|null $model_id (optionnal)
     *
     * @return void
     */
    public function construct(int $model_id = null)
    {
        if (!isset(static::$post_type) || static::$post_type == null) {
            throw new Exception("Model's post_type must be registred");
        }

        if ($model_id === null) {
            $model_id = $this->create();
        }

        $post = get_post($model_id);

        if ($post === null) {
            throw new Exception("Failed to retreive post");
        } else {
            $this->post_id = $model_id;
        }
    }



    /// *****  Class functions  ***** ///


    /**
     * Return post_id
     *
     * @return int post_id
     */
    public function id()
    {
        return $this->post_id;
    }


    /**
     * Create a new Model in database
     *
     * @return int $post_id
     */
    public function create()
    {
        return wp_insert_post(
            [
                'post_title'    => 'Draft title',
                'post_status'   => 'draft',
                'post_type'     => static::$post_type
            ]
        );
    }


    /**
     * Publish the current post
     *
     * @return void
     */
    public function publish()
    {
        wp_publish_post($this->post_id);
    }


    /**
     * Trash the current post
     *
     * @return void
     */
    public function trash()
    {
        wp_trash_post($this->post_id);
    }


    /**
     * Delete permanently the current post
     *
     * @return void
     */
    public function delete()
    {
        wp_delete_post($this->post_id, true);
    }


    /**
     * Retrieve post from database
     *
     * @return \WP_post|array
     */
    public function post($output = OBJECT)
    {
        return get_post($this->post_id, $output);
    }


    /**
     * Retrieve post metas from database
     *
     * @return array
     */
    public function metas()
    {
        return get_post_custom($this->post_id);
    }


    /**
     * Retrieve post metas keys from database
     *
     * @return array
     */
    public function metasKeys()
    {
        return array_keys($this->metas());
    }



    /** Getters **/



    /**
     * Get a post meta based on meta key
     *
     * @param string $meta_key key colrresponding to the meta
     * @param bool   $single   optional $single see get_post_meta() single opt
     *
     * @return array|string|int
     */
    public function getMeta(string $meta_key, bool $single = true)
    {
        return get_post_meta($this->post_id, $meta_key, $single);
    }


    /**
     * Retrieve post taxonomies
     *
     * @return array
     */
    public function getTaxonomies()
    {
        $values = array();
        $taxonomies = get_post_taxonomies($this->post_id);

        foreach ($taxonomies as $taxonomy) {
            $values[$taxonomy] = get_terms(
                [
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false
                ]
            );
        }
        return $values;
    }


    /**
     * Update post to database
     *
     * @param WP_Post|array $post post data to be updated
     *
     * @return void
     */
    protected function updatePost($post)
    {
        wp_update_post($post);
    }



    /** Setters **/



    /**
     * Update post's given property
     *
     * @param string $property property key to be affected
     * @param mixed $value value to set
     *
     * @return void
     */
    public function setPostProperty(string $property, $value)
    {
        $post = $this->post(ARRAY_A);
        $post[$property] = $value;
        $this->updatePost($post);
    }


    /**
     * Update post's given properties
     *
     * @param array_a $args Associative array [property_key => value]
     *
     * @return void
     */
    public function setPostProperties(array $args)
    {
        $post = $this->post(ARRAY_A);

        foreach ($args as $key => $value) {
            $post[$key] = $value;
        }

        $this->updatePost($post);
    }


    /**
     * Update post meta
     *
     * @param string $key      name of post metas to be updated in the database
     * @param mixed  $value    value of post metas to be updated in the database
     * @param bool   $multiple tells is the meta key unique or not
     *
     * @return void
     */
    public function setMeta(string $key, $value, $multiple = false)
    {
        if ($multiple === false) {
            return update_post_meta($this->post_id, $key, $value);
        } else {
            return add_post_meta($this->post_id, $key, $value, false);
        }
    }


    /**
     * Update post meta
     *
     * @param array $metas ['meta_key' => 'meta_value']
     *
     * @return void
     */
    public function setMetas(array $metas)
    {
        foreach ($metas as $key => $value) {
            $this->setMeta($key, $value);
        }
    }


    /**
     * Update Property's taxonomies.
     *
     * @param string $taxonomy taxonomy key
     * @param array  $terms    terms list
     *
     * @return void
     */
    public function setTaxonomy(string $taxonomy, array $terms)
    {
        wp_set_post_terms($this->post_id, [], $taxonomy, false);
        wp_set_post_terms($this->post_id, $terms, $taxonomy, false);
    }
}
