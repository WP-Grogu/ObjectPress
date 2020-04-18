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


    /**
     * Check if a post is part of the specified post type.
     * Return false on failure / post type doesn't exists
     *
     * @param string|int|\WP_Post $post      Post to check on
     * @param string              $post_type Post type to checkup
     *
     * @return bool
     */
    public static function isA($post, string $post_type): bool
    {
        $p_type = get_post_type($post);

        if ($p_type === false) {
            return false;
        }

        return $p_type === $post_type;
    }


    /**
     * Excerpt
     *
     * @param $text, $length
     */
    public function excerpt($text, $length)
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        $text = mb_substr($text, 0, $length);
        if (mb_substr($text, $length - 1, 1) !== ' ') {
            $parts = explode(' ', $text);
            array_pop($parts);
            $text = implode(' ', $parts);
        }
        return trim($text) . '…';
    }
}
