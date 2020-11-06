<?php

namespace OP\Framework\Helpers;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.3
 * @access   public
 * @since    1.0
 */
class LanguageHelper
{
    /**
     * Return the current language
     *
     * @return string
     * @deprecated since version 1.0.4
     */
    public static function currentLang()
    {
        return static::getCurrentLang();
    }


    /**
     * Return the current language
     *
     * @return string
     * @since 1.0.4
     */
    public static function getCurrentLang()
    {
        // PolyLang
        if (function_exists('pll_current_language')) {
            return pll_current_language('slug');
        }


        // WPML
        if (defined('ICL_LANGUAGE_CODE')) {
            return ICL_LANGUAGE_CODE;
        }
    }


    /**
     * Return the current language
     *
     * @param string $lang the design language slug
     *
     * @return bool
     * @since 1.0.4
     */
    public static function setCurrentLang(string $lang)
    {
        // PolyLang
        if (function_exists('PLL')) {
            PLL()->curlang = PLL()->model->get_language($lang);
            return true;
        }

        // WPML
        if (defined('ICL_LANGUAGE_CODE')) {
            do_action('wpml_switch_language', $lang);
            return true;
        }

        return false;
    }


    /**
     * Return the primary language
     *
     * @return string|null
     */
    public static function primaryLang()
    {
        // PolyLang
        if (function_exists('pll_default_language')) {
            return (string) pll_default_language('slug');
        }


        // WPML
        if (function_exists('wpml_get_language_information')) {
            $wpml_options = get_option('icl_sitepress_settings');
            return (string) $wpml_options['default_language'] ?: '';
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
    public static function getPostLang(int $id)
    {
        // PolyLang
        if (function_exists('pll_get_post_language')) {
            return (string) pll_get_post_language($id, 'slug');
        }


        // WPML
        if (function_exists('wpml_get_language_information')) {
            $infos = wpml_get_language_information(null, $id);
            if (is_array($infos) && array_key_exists('language_code', $infos)) {
                return $infos['language_code'];
            }
            return '';
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
        // PolyLang
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($id, $lang);
            return true;
        }

        // WPML
        if (function_exists('wpml_get_language_information')) {
            $infos = wpml_get_language_information(null, $id);
            if (is_array($infos) && array_key_exists('language_code', $infos)) {
                $infos['language_code'] = $lang;
                do_action('wpml_set_element_language_details', $set_language_args);
            }
            return true;
        }

        return false;
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
        // PolyLang
        if (function_exists('pll_save_post_translations')) {
            pll_save_post_translations($assoc);
        }
    }


    /**
     * Get post in desired $lang
     *
     * @param string $post Post
     * @param string $lang Language slug to get
     *
     * @return int
     */
    public static function getPostIn($post, string $lang)
    {
        $post = PostHelper::getPostFromUndefined($post);

        if (!$post) {
            return false;
        }

        // PolyLang
        if (function_exists('pll_get_post')) {
            return pll_get_post($post->ID, $lang);
        }

        // WPML
        if (array_key_exists('wpml_object_id', $GLOBALS['wp_filter'])) {
            return apply_filters('wpml_object_id', $post->ID, $post->post_type, false, $lang);
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
        // PolyLang
        if (function_exists('pll_get_term')) {
            return pll_get_term($t_id, $lang);
        }
    }


    /**
     * Get a i18n translated string in desired lang
     *
     * @param
     * @return string
     */
    public static function getStringIn(string $string, string $domain, string $lang)
    {
        // WPML
        if (function_exists('wpml_get_language_information')) {
            $base_lang = static::currentLang();
            $string = __($string, $domain);

            if ($lang !== $base_lang) {
                do_action('wpml_switch_language', $lang);
                $string = __($string, $domain);
                do_action('wpml_switch_language', $base_lang);
            }

            return $string;
        }
    }


    /**
     * Get a i18n translated string in desired lang
     *
     * @param
     * @return string|null
     */
    public static function getPostPermalinkIn($post, ?string $lang)
    {
        if (!$lang) {
            return get_permalink($post);
        }
        
        $permalink = false;

        // WPML
        if (function_exists('wpml_get_language_information')) {
            $base_lang = static::currentLang();

            if ($lang !== $base_lang) {
                do_action('wpml_switch_language', $lang);
                $permalink = get_permalink($post);
                do_action('wpml_switch_language', $base_lang);
            } else {
                $permalink = get_permalink($post);
            }
        }
        
        return $permalink;
    }
}
