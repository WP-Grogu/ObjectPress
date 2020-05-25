<?php

namespace OP\Framework\Models\Factories;

use Exception;
use OP\Framework\Helpers\PostHelper;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.2.1
 * @access   public
 * @since    0.1
 */
class PostModelFactory
{
    /**
     * Factory model, get the post type and initiate a corresponding Model if applicable
     *
     * @param WP_post|int|string    $post_id   ID of the concerned post
     *
     * @return Model
     * @version 1.2.1
     * @since 0.1
     */
    public static function model($post)
    {
        $post  = PostHelper::getPostFromUndefined($post);
        $model = null;
        
        if (!$post) {
            return null;
        }

        // Search for model in ICpts array
        if (
            interface_exists('\App\Interfaces\ICpts')
            && defined('\App\Interfaces\ICpts::MODELS')
            && is_array(\App\Interfaces\ICpts::MODELS)
            && array_key_exists($post->post_type, \App\Interfaces\ICpts::MODELS)
          ) {
            $supposed_model = \App\Interfaces\ICpts::MODELS[$post->post_type];

            if (class_exists($supposed_model)) {
                $model = $supposed_model;
            } else {
                throw new Exception(
                    "ObjectPress: The `$supposed_model` model does not exists for post type `$post->post_type`. Please checkup your MODELS binding in you ICPTs Interface."
                );
            }
        } else {
            // Try to guess class model name (eg: 'custom-post' => 'App\Models\CustomPost')
            $supposed_class_name    = str_replace('-', '', ucwords($post->post_type, '-'));
            $full_supposed_class    = "\App\Models\\$supposed_class_name";

            if (class_exists($full_supposed_class)) {
                $model = $full_supposed_class;
            }
        }

        if ($model) {
            return $model::find($post->ID);
        }

        return $model;
    }
}
