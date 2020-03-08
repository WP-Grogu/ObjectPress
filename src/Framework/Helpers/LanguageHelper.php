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
class LanguageHelper
{
    /**
     * Get a post language
     *
     * @param int    $id
     * @param string $lang
     *
     * @return string|void
     * @since 0.1
     */
    public static function getPostLang(int $id): string
    {
        if (function_exists('pll_get_post_language')) {
            return (string) pll_get_post_language($id, 'slug');
        }
    }

    /**
     * Set a post language
     *
     * @param int    $id
     * @param string $lang
     *
     * @return void
     * @since 0.1
     */
    public static function setPostLang(int $id, string $lang)
    {
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($id, $lang);
        }
    }

    /**
     * Synchronize 2 posts as translation of each other
     *
     * @param array $assoc Post association, as ['fr' => $post_id, 'en' => $post_id]
     *
     * @return void
     * @since 0.1
     */
    public static function syncPosts(array $assoc): void
    {
        if (function_exists('pll_save_post_translations')) {
            pll_save_post_translations($assoc);
        }
    }
}
