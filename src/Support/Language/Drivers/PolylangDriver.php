<?php

namespace OP\Support\Language\Drivers;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    2.0
 */
class PolylangDriver extends AbstractDriver
{
    /**
     * Return the current language
     *
     * @return string
     * @since 2.0
     */
    public function getCurrentLang()
    {
        return pll_current_language('slug');
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
        try {
            PLL()->curlang = PLL()->model->get_language($lang);
            return true;
        } catch (\Exception $e) {
            return false;
        }
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
        global $polylang;

        return $polylang->model->get_languages_list();
    }


    /**
     * Return the primary language
     *
     * @param string $as The return format. (can be: slug, local, name)
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
     * @param string $as The return format. (can be: slug, local, name)
     *
     * @return string|null
     * @since 2.0
     */
    public function getPrimaryLang(string $as = 'slug'): ?string
    {
        return (string) pll_default_language('slug');
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
        return (string) pll_get_post_language($id, 'slug');
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
        pll_set_post_language($id, $lang);
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
        pll_save_post_translations($assoc);
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

        return pll_get_post($post->ID, $lang);
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
        return (int) pll_get_term($t_id, $lang);
    }


    /**
     * Get a i18n translated string in desired language.
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
        // @todo
        return '';
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
        // @todo
        return null;
    }
}
