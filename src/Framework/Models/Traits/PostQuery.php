<?php

namespace OP\Framework\Models\Traits;

use OP\Framework\Helpers\PostHelper;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.2
 * @access   public
 * @since    0.2
 */
trait PostQuery
{
    /**
     * Get current post using wordpress magic function
     *
     * @return Model null on failure
     * @since 0.1
     */
    public static function current()
    {
        $id = get_the_id();

        if (!$id) {
            global $post;
            $id = $post->ID ?? false;
        }

        if (!$id) {
            return null;
        }

        return new static($id);
    }


    /**
     * Create a new Post. Returns self, false on failure
     *
     * @param array_a Post attributes (optionnal)
     *
     * @return this|false on failure
     * @since 0.1
     */
    public static function insert(array $attributes = [])
    {
        $post = new static();

        if ($post && $post->id) {
            $post->setPostProperties($attributes);
            return $post;
        }

        return false;
    }


    /**
     * Find the ressource(s)
     *
     * @param array $post_ids Post ids to get
     *
     * @return array
     * @since 1.2.1
     */
    public static function collection(array $post_ids)
    {
        $collection = [];

        foreach ($post_ids as $post_id) {
            $item = static::find($post_id);

            if (is_bool($item) && $item === false) {
                continue;
            }

            $collection[] = $item;
        }

        return $collection;
    }


    /**
     * Get all the properties IDs from database
     *
     * @param int   $limit   Maximum posts to retrive
     * @param array $status  Post status to retreive, default to 'publish' status
     *
     * @return array of Model
     * @since 0.1
     */
    public static function all(?int $limit = null, array $status = ['publish'])
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $posts = [];

        $status = implode("', '", $status);

        $query = "  SELECT ID 
                    FROM {$prefix}posts 
                    WHERE post_type = '" . static::$post_type . "' 
                    AND post_status IN ('$status')
                    ORDER BY ID DESC
                ";

        if ($limit != null) {
            $query .= " LIMIT {$limit}";
        }

        $results = $wpdb->get_results($query);

        foreach ($results as $result) {
            $posts[] = new static($result->ID);
        }

        return $posts;
    }


    /**
     * Get the first n post of current model
     *
     * @param int   $limit   Number of posts to retrive (n)
     * @param array $status  Post status to retreive, default to 'publish' status
     *
     * @return array of Model
     * @since 0.2
     */
    public static function first(int $limit = 1, array $status = ['publish'])
    {
        return static::all($limit, $status);
    }


    /**
     * Find the ressource, or return false
     *
     * @since 1.2.1
     */
    public static function find($post_id)
    {
        if (!static::belongsToModel($post_id)) {
            return false;
        }

        return new static($post_id);
    }


    /**
     * Check if the given post(s) are member of the current model
     * If you're giving an array of posts, reuturns true if ALL of them are members
     *
     * @param array|string|int|WP_Post $post Post(s) to checkup.
     *
     * @return bool
     */
    public static function belongsToModel($posts): bool
    {
        if (!$posts || (is_array($posts) && empty($posts))) {
            return false;
        }

        if (!is_array($posts)) {
            $posts = [$posts];
        }

        foreach ($posts as $post) {
            if (!PostHelper::isA($post, static::$post_type)) {
                return false;
            }
        }

        return true;
    }
}
