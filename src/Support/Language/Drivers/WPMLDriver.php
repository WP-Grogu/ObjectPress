<?php

namespace OP\Support\Language\Drivers;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    2.0
 */
class WPMLDriver extends AbstractDriver
{
    /**
     * Return the current language
     *
     * @return string
     * @since 2.0
     */
    public function getCurrentLang()
    {
        return ICL_LANGUAGE_CODE;
    }


    /**
     * Return the current language
     *
     * @param string $lang the desired language slug.
     *
     * @return bool
     * @since 2.0
     */
    public function setCurrentLang(string $lang)
    {
        do_action('wpml_switch_language', $lang);
        return true;
    }


    /**
     * Get available languages on this app.
     *
     * @return array
     * @since 2.0
     */
    public function getLanguages(): array
    {
        return $this->getAvailableLanguages();
    }


    /**
     * Get available languages on this app.
     *
     * @return array
     * @since 2.0
     */
    public function getAvailableLanguages(): array
    {
        return apply_filters('wpml_active_languages', null);
    }


    /**
     * Return the primary language
     *
     * @return string|null
     * @since 2.0
     */
    public function primaryLang(string $as = 'slug'): ?string
    {
        return $this->getPrimaryLang();
    }


    /**
     * Return the primary language
     *
     * @return string|null
     * @since 2.0
     */
    public function getPrimaryLang(string $as = 'slug'): ?string
    {
        return (string) apply_filters('wpml_default_language', null);
    }


    /**
     * Get a post language
     *
     * @param int    $id
     * @param string $lang
     *
     * @return string|void
     * @since 2.0
     */
    public function getPostLang(int $id)
    {
        $infos = wpml_get_language_information(null, $id);
        
        if (is_array($infos) && array_key_exists('language_code', $infos)) {
            return $infos['language_code'];
        }

        return '';
    }


    /**
     * Set a post language
     *
     * @param int    $id
     * @param string $lang
     *
     * @return void
     * @since 2.0
     */
    public function setPostLang(int $id, string $lang): void
    {
        $infos = wpml_get_language_information(null, $id);
        
        if (is_array($infos) && array_key_exists('language_code', $infos)) {
            $infos['language_code'] = $lang;
            do_action('wpml_set_element_language_details', $infos);
        }
    }


    /**
     * Synchronize 2 posts as translation of each other
     *
     * @param array $assoc Post association, as ['fr' => $post_id, 'en' => $post_id]
     *
     * @return void
     * @since 2.0
     */
    public function syncPosts(array $assoc): void
    {
        // @todo
    }


    /**
     * Get post in desired $lang
     *
     * @param int|WP_Post|ARRAY_A  $post  Post
     * @param string               $lang  Language to get, as slug
     *
     * @return int
     * @since 2.0
     */
    public function getPostIn($post, string $lang)
    {
        $post = PostHelper::getPostFromUndefined($post);

        if (!$post) {
            return false;
        }

        return apply_filters('wpml_object_id', $post->ID, $post->post_type, false, $lang);
    }


    /**
     * Get Taxonomy Term in desired $lang
     *
     * @param string $lang Language slug to get the term in
     * @param int    $t_id The term id
     *
     * @return int
     * @since 2.0
     */
    public function getTermIn(string $lang, string $t_id): int
    {
        // @todo
        return 0;
    }


    /**
     * Get a i18n translated string in desired lang
     *
     * @param string $string String to translate
     * @param string $domain i18n Domain
     * @param string $lang The language slug
     *
     * @return string
     * @since 2.0
     */
    public function getStringIn(string $string, string $domain, string $lang)
    {
        $base_lang = $this->getCurrentLang();
        $string    = __($string, $domain);

        if ($lang !== $base_lang) {
            do_action('wpml_switch_language', $lang);
            $string = __($string, $domain);
            do_action('wpml_switch_language', $base_lang);
        }

        return $string;
    }


    /**
     * Get the post permalink in desired language.
     *
     * @param int|WP_Post|ARRAY_A  $post  The post
     * @param string               $lang  The desired language
     *
     * @return string|false
     * @since 2.0
     */
    public function getPostPermalinkIn($post, string $lang)
    {
        $base_lang = $this->getCurrentLang();

        if ($lang !== $base_lang) {
            do_action('wpml_switch_language', $lang);
            $permalink = get_permalink($post);
            do_action('wpml_switch_language', $base_lang);
        } else {
            $permalink = get_permalink($post);
        }
        
        return $permalink;
    }
}
