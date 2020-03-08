<?php

namespace OP\Framework\Helpers;

use OP\Framework\Models\PostModel;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.1
 * @access   public
 * @since    0.1
 */
class PostHelper
{
    /**
     * Quickly updates some Post properties
     *
     * @param int   $post_id
     * @param array $properties ARRAY_A
     *
     * @return void
     */
    public static function setPostPoperties(int $post_id, array $properties)
    {
        $post = get_post($post_id, ARRAY_A);

        if ($post) {
            foreach ($properties as $key => $value) {
                $post[$key] = $value;
            }
            wp_update_post($post);
        }
    }
}
