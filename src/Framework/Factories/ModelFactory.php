<?php

namespace OP\Framework\Factories;

use OP\Support\Facades\Config;
use OP\Framework\Helpers\PostHelper;
use OP\Framework\Exceptions\ClassNotFoundException;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0.0
 * @access   public
 * @since    1.0.0
 */
class ModelFactory
{
    /**
     * Factory 'post' model, get the post type and initiate a corresponding Model if applicable.
     *
     * @param WP_post|int|string    $post_id   ID of the concerned post
     *
     * @return Model|null on failure
     * @version 1.0.3
     * @since 1.0.1
     */
    public static function post($post)
    {
        $post  = PostHelper::getPostFromUndefined($post);
        $psr   = Config::get('object-press.theme.psr-prefix') ?: 'App';
        $conf  = Config::get('setup.models') ?: [];
        $model = null;

        if (!$post) {
            return null;
        }

        // Search for model in configuration array.
        if (!empty($conf) && array_key_exists($post->post_type, $conf)) {
            $supposed_class_name = $conf[$post->post_type];

            if (class_exists($supposed_class_name)) {
                $model = $supposed_class_name;
            } else {
                throw new ClassNotFoundException(
                    "ObjectPress: The `$supposed_class_name` model does not exists for post type `$post->post_type`. Please checkup your setup.php configuration file."
                );
            }
        } else {
            // Try to guess class model name (eg: 'custom-post-type' => 'App\Models\CustomPostType')
            $supposed_class_name = str_replace('-', '', ucwords($post->post_type, '-'));
            $full_supposed_class = sprintf('%s\Models\%s', $psr, $supposed_class_name);
            $op_supposed_class   = sprintf('OP\Framework\Models\%s', $supposed_class_name);

            if (class_exists($full_supposed_class)) {
                $model = $full_supposed_class;
            }

            if (!$model && class_exists($op_supposed_class)) {
                $model = $op_supposed_class;
            }
        }

        if ($model) {
            return $model::find($post->ID);
        }

        return null;
    }


    /**
     * Call the model factory on the current post
     *
     * @return Model|null on failure
     * @version 1.0.4
     * @since 1.0.4
     */
    public static function currentPost()
    {
        $id = get_the_id();

        if (!$id) {
            global $post;
            $id = $post->ID ?? false;
        }

        if (!$id) {
            return null;
        }

        return static::post($id);
    }


    /**
     * Factory an array of 'post' model, get the post type and initiate a corresponding Model if applicable.
     *
     * @param array $posts WP_post|int|string of the concerned posts
     *
     * @return array
     * @version 1.0.4
     * @since 1.0.4
     */
    public static function posts(array $posts)
    {
        $results = [];

        foreach ($posts as $post) {
            $results[] = static::post($post);
        }
        
        return $results;
    }
}
