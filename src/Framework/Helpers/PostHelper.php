<?php

namespace OP\Framework\Helpers;

use OP\Support\Facades\Config;
use OP\Framework\Factories\ModelFactory;
use OP\Framework\Exceptions\BadConfigurationException;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.0
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
     * Get generate an excerpt from content text and maximum length allowed.
     *
     * @param string  $text
     * @param int     $length
     *
     * @return string
     */
    public static function excerpt(string $text, $length = 200)
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


    /**
     * Transform post_id to WP_Post Object; from string, int or ARRAY_A
     *
     * @param string|int|array|WP_Post $post
     *
     * @return WP_Post|false
     */
    public static function getPostFromUndefined($post)
    {
        if (!$post) {
            return false;
        }

        if (is_string($post)) {
            $post = intval($post);
        }
        
        if (is_int($post)) {
            $post = get_post($post);
        }

        if (is_array($post) && isset($post['ID'])) {
            $post = get_post($post['ID']);
        }

        if (!is_a($post, 'WP_Post')) {
            return false;
        }

        return $post;
    }


    /**
     * Grab IDs from arrays, based on 'ID' array key (works for stdClass/WP_Post objects)
     *
     * eg: [0 => WP_Post[ 'ID' => 1 ], 1 => WP_Post[ 'ID' => 2 ]]
     *
     * @return array Array of IDs
     */
    public static function grabIDS(array $items)
    {
        $items = array_map(function ($e) {
            if (is_array($e) && array_key_exists('ID', $e)) {
                return $e['ID'];
            }
            return $e->ID ?? null;
        }, $items);

        if ($items && is_array($items)) {
            $items = array_filter($items);
        }

        return $items;
    }



    /**
     * Find the post, or return false
     *
     * @param string  $identifier Post identifier (av. : 'id', 'url', 'slug')
     * @param *       $value
     *
     * @return WP_Post
     * @since 1.2.1
     */
    public static function getPostBy($identifier, $value, $_ptype = 'page')
    {
        if ($identifier === 'id') {
            return static::getPostFromUndefined($value);
        }

        if ($identifier === 'url') {
            $p_id = url_to_postid($value);
            return static::getPostFromUndefined($p_id);
        }
        
        if ($identifier === 'slug') {
            return get_page_by_path($value, OBJECT, $_ptype) ?: false;
        }
    }


    /**
     * Get pages associated to a template.
     *
     * @param  string    $tpml   Template name (eg: 'blog' for 'template-blog.php')
     * @param  int|null  $limit  If set, only return this number of page(s). 0 for all posts.
     *
     * @return array of Page
     */
    public static function getTemplatePages(string $tmpl, ?int $limit = 0)
    {
        $structure = Config::get('object-press.wp.template-files-structure');

        // Check if there is a %s inside the string, otherwise throw exception.
        if (substr_count($structure, '%s') !== 1) {
            throw new BadConfigurationException(
                "ObjectPress : Invalid `object-press.wp.template-files-structure` configuration. You must exactly include one `%s` in your string."
            );
        }

        // Build up regex based on the structural configuration.
        $regex = sprintf(
            '%s' . str_replace('/', '\/', preg_quote($structure)) . '%s',
            '/^',
            '[a-zA-Z0-9_.-]',
            '$/'
        );

        // Replace 'example' to 'template-example.php'
        if (!preg_match($regex, $tmpl)) {
            $tmpl = sprintf($structure, $tmpl);
        }

        $params = [
            'meta_key'     => '_wp_page_template',
            'meta_value'   => $tmpl,
            'hierarchical' => false,                 // Take children pages as well
            'number'       => abs($limit),
        ];

        return get_pages($params);
    }
}
