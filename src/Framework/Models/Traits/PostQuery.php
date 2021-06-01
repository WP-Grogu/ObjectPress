<?php

namespace OP\Framework\Models\Traits;

use OP\Framework\Helpers\PostHelper;
use WP_Query;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.4
 * @access   public
 * @since    1.0.1
 */
trait PostQuery
{
    /**
     * Get current post using wordpress magic function
     *
     * @return Model null on failure
     * @since 1.0.0
     */
    public static function current()
    {
        $id = get_the_id();

        if (!$id) {
            global $post;
            $id = $post->ID ?? false;
        }

        if (!$id || !static::belongsToModel($id)) {
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
     * @since 1.0.0
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
     * Get all the posts from database
     *
     * @param int   $limit   Maximum posts to retrive
     * @param array $status  Post status to retreive, default to 'publish' status
     *
     * @return array of Model
     * @since 1.0.0
     */
    public static function all(?int $limit = null, array $status = ['publish'])
    {
        $posts   = [];
        $results = static::allIds($limit, $status);

        foreach ($results as $result) {
            $posts[] = new static($result);
        }

        return $posts;
    }


    /**
     * Get all the ids from post from database
     *
     * @param int   $limit   Maximum posts to retrive
     * @param array $status  Post status to retreive, default to 'publish' status
     *
     * @return array of Model
     * @since 1.0.4
     */
    public static function allIds(?int $limit = null, array $status = ['publish'])
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

        return array_map(function ($e) {
            return (int) $e->ID;
        }, $results);
    }


    /**
     * Get the first n ($take) post of current model
     *
     * @param int   $take   Number of posts to retrive (n)
     * @param array $status  Post status to retreive, default to 'publish' status
     *
     * @return array of Model
     * @since 1.0.1
     */
    public static function first(int $take = 1, array $status = ['publish'])
    {
        return static::all($take, $status);
    }


    /**
     * Find the ressource and returns and instance of self, or return false
     *
     * @param int|string|WP_Post|ARRAY_A $post
     *
     * @return self
     * @since 1.2.1
     */
    public static function find($post)
    {
        $post = PostHelper::getPostFromUndefined($post);

        if (!$post || !static::belongsToModel($post->ID)) {
            return false;
        }

        return new static($post->ID);
    }


    /**
     * Find the ressource by slug, or return false
     *
     * @param string  $slug Post slug
     *
     * @return self|false
     * @since 1.2.1
     */
    public static function findBySlug(string $slug)
    {
        $post = get_page_by_path($slug, OBJECT, static::$post_type);

        return $post ? static::find($post->ID) : false;
    }


    /**
     * Find the ressource, or return false
     *
     * @param string  $identifier Post identifier ('id', 'url', 'slug')
     * @param *       $value
     *
     * @return self
     * @since 1.2.1
     */
    public static function findBy($identifier, $value)
    {
        $post = PostHelper::getPostBy($identifier, $value, static::$post_type);

        if (!$post || !static::belongsToModel($post->ID)) {
            return false;
        }

        return new static($post->ID);
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


    /**
     * Get the posts from a pagination.
     * Specify how many post you need per pages, and the page you wish to retrive.
     * Returns current page being viewed, max number of pages, and current page items.
     *
     * @param int   $per_page  The number of posts per pages.
     * @param int   $page      The page number to get.
     * @param array $args      WP_Query arguments to override if needed.
     *
     * @return array
     */
    public static function paginate(int $per_page, int $page = 1, array $args = []): array
    {
        $op_args = array(
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_status'    => 'publish',
            'post_type'      => static::$post_type,
            'posts_per_page' => $per_page,
            'paged'          => absint($page),
            'fields'         => 'ids',
        );

        $query = new WP_Query($args + $op_args);

        $max_page = (int) $query->max_num_pages;

        return [
            'page'     => $max_page ? absint($page) : 0,
            'max_page' => $max_page,
            'items'    => static::collection($query->posts),
        ];
    }
}
