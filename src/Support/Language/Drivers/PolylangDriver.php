<?php

namespace OP\Support\Language\Drivers;

use AmphiBee\Eloquent\Connection;
use OP\Framework\Helpers\PostHelper;

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
     * The instance cache to avoid repeating databse queries.
     *
     * @var array
     */
    private $cache = [];

    /**
     * Return the current language
     *
     * @return string
     * @since 2.0
     */
    public function getCurrentLang(string $as = 'slug')
    {
        return pll_current_language($as);
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
            PLL()->curlang = PLL()->model->get_language($this->localeToSlug($lang));
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

        return $polylang ? $polylang->model->get_languages_list() : [];
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
        return $this->getPrimaryLang($as);
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
        return function_exists('pll_default_language')
            ? (string) pll_default_language($as)
            : null;
    }



    /**************************************/
    /*                                    */
    /*               Posts                */
    /*                                    */
    /**************************************/



    /**
     * Get a post language
     *
     * @param int    $id
     *
     * @return string|void
     * @since 2.0
     */
    public function getPostLang(int $id, string $field = 'slug')
    {
        return function_exists('pll_default_language')
            ? (string) pll_get_post_language($id, $field)
            : null;
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
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($id, $this->localeToSlug($lang));
        }
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

        return pll_get_post($post->ID, $this->localeToSlug($lang));
    }


    /**
     * Synchronize multiple posts as translation of each other
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
     * Get all ids from posts in the asked language
     *
     * @param string $lang The language slug
     *
     * @return array
     */
    public function postsInLang(string $lang)
    {
        global $wpdb;

        if (isset($this->cache['ids'][$lang])) {
            return $this->cache['ids'][$lang];
        }

        $db     = Connection::instance();
        $prefix = $db->getPdo()->prefix();

        $ids = $wpdb->get_results("
            SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(description, '\"{$lang}\";i:', -1), ';',1) as id
            FROM `{$prefix}term_taxonomy`
            WHERE `taxonomy` = 'post_translations'
        ");

        $ids = collect($ids)->groupBy('id')->keys()->toArray();

        if (!isset($this->cache['ids'])) {
            $this->cache['ids'] = [];
        }

        $this->cache['ids'][$lang] = $ids;

        return $ids;
    }


    /**
     * Get all post translation ids.
     *
     * @return array
     */
    public function getPostIds($post_id)
    {
        global $polylang;

        $ids       = [];
        $languages = $polylang->model->get_languages_list();

        foreach ($languages as $language) {
            $ids[$language->locale] = $polylang->model->post->get($post_id, $language->slug);
        }

        return array_filter($ids);
    }


    /**************************************/
    /*                                    */
    /*               Terms                */
    /*                                    */
    /**************************************/



    /**
     * Get a term language
     *
     * @param int    $id
     *
     * @return string|void
     * @since 2.0
     */
    public function getTermLang(int $id, string $field = 'slug')
    {
        return (string) pll_get_term_language($id, $field);
    }


    /**
     * Set a term language
     *
     * @param int    $id The term id
     * @param string $lang
     *
     * @return void
     * @since 2.0
     */
    public function setTermLang(int $id, string $lang): void
    {
        pll_set_term_language($id, $this->localeToSlug($lang));
    }


    /**
     * Get a term translations in array_a
     * [locale => id]
     *
     * @param int    $id
     *
     * @return string|void
     * @since 2.0
     */
    public function getTermTranslations(int $id)
    {
        $langs        = $this->getAvailableLanguages();
        $translations = [];

        foreach ($langs as $lang) {
            $translations[$lang->name] = $this->getTermIn($id, $lang->name) ?: null;
        }

        return $translations;
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
        return (int) pll_get_term($t_id, $this->localeToSlug($lang));
    }


    /**
     * Synchronize multiple terms as translation of each other
     *
     * @param array $assoc Post association, as ['fr' => $term_id, 'en' => $term_id]
     *
     * @return void
     * @since 2.0
     */
    public function syncTerms(array $assoc): void
    {
        pll_save_term_translations($assoc);
    }



    /**************************************/
    /*                                    */
    /*              Strings               */
    /*                                    */
    /**************************************/



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
        return pll_translate_string($string, $this->localeToSlug($lang));
    }


    /**
     * Set a registred string translation in given language.
     *
     * @param string $string       String to translate
     * @param string $translation  The translation
     * @param string $lang         The language locale/slug
     *
     * @return bool
     * @since 2.0
     */
    public function setStringIn(string $string, string $translation, string $lang)
    {
        $lang      = $this->localeToSlug($lang);
        $available = $this->getAvailableLanguages();
        $mo_id     = 0;
        $found     = false;

        // Get mo_id
        foreach ($available as $av) {
            if ($av->slug === $lang) {
                $mo_id = $av->mo_id;
                break;
            }
        }

        if (!$mo_id) {
            return false;
        }

        $strings = get_post_meta($mo_id, '_pll_strings_translations', true) ?: [];

        // Look for the string inside the translation array
        foreach ($strings as $i => $entry) {
            if (is_array($entry) && $entry[0] === $string) {
                $strings[$i][1] = $translation;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $strings[] = [$string, $translation];
        }

        $strings = wp_slash($strings);

        return (bool) update_post_meta($mo_id, '_pll_strings_translations', $strings);
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


    /**
     * Register a string for translation.
     *
     * @param string $string   The string to register
     * @param string $name     A unique name for the string
     * @param string $group    (Optional) The group in which the string is registered
     *
     * @return
     */
    public function registerString(string $string, string $name, string $group = 'op-theme'): void
    {
        pll_register_string($name, $string, $group);
    }
}
