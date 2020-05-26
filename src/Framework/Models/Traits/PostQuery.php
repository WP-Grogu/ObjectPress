<?php

namespace OP\Framework\Models\Traits;

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
}
