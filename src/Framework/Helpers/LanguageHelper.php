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
     * Return the current language
     *
     * @return string
     */
    public static function currentLang()
    {
        if (function_exists('pll_current_language')) {
            return pll_current_language('slug');
        }
    }


    /**
     * Return the primary language
     *
     * @return string
     */
    public static function primaryLang()
    {
        if (function_exists('pll_default_language')) {
            return pll_default_language('slug');
        }
    }


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


    /**
     * Get post in desired $lang
     *
     * @param string $lang Language slug to get
     * @return int
     */
    public static function getPostIn(string $lang, string $p_id)
    {
        if (function_exists('pll_get_post')) {
            return pll_get_post($p_id, $lang);
        }
    }


    /**
     * Get Taxonomy Term in desired $lang
     *
     * @param string $lang Language slug to get
     * @return int
     */
    public static function getTermIn(string $lang, string $t_id)
    {
        if (function_exists('pll_get_term')) {
            return pll_get_term($t_id, $lang);
        }
    }
}
