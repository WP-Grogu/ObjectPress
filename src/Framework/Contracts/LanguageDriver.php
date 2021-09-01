<?php

namespace OP\Framework\Contracts;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    2.0
 */
interface LanguageDriver
{
    /**
     * Return the current language
     *
     * @return string
     * @since 2.0
     */
    // public function getCurrentLang(string $as = 'slug');


    /**
     * Return the current language
     *
     * @param string $lang the desired language slug.
     *
     * @return bool
     * @since 2.0
     */
    public function setCurrentLang(string $lang);


    /**
     * Get available languages on this app.
     *
     * @return array
     * @since 2.0
     */
    public function getLanguages(): array;


    /**
     * Get available languages on this app.
     *
     * @return array
     * @since 2.0
     * @todo Refacto to use a Normalizer
     */
    public function getAvailableLanguages(): array;


    /**
     * Return the primary language
     *
     * @param string $as The return format. (can be: slug, local, name)
     *
     * @return string|null
     * @since 2.0
     */
    public function primaryLang(string $as = 'slug'): ?string;


    /**
     * Return the primary language
     *
     * @param string $as The return format. (can be: slug, local, name)
     *
     * @return string|null
     * @since 2.0
     */
    public function getPrimaryLang(string $as = 'slug'): ?string;

    /**
     * Get a post language
     *
     * @param int    $id
     * @param string $lang
     *
     * @return string|void
     * @since 2.0
     */
    public function getPostLang(int $id, string $field = 'slug');


    /**
     * Set a post language
     *
     * @param int    $id
     * @param string $lang
     *
     * @return void
     * @since 2.0
     */
    public function setPostLang(int $id, string $lang): void;


    /**
     * Synchronize 2 posts as translation of each other
     *
     * @param array $assoc Post association, as ['fr' => $post_id, 'en' => $post_id]
     *
     * @return void
     * @since 2.0
     */
    public function syncPosts(array $assoc): void;


    /**
     * Get post in desired $lang
     *
     * @param int|WP_Post|ARRAY_A  $post  Post
     * @param string               $lang  Language to get, as slug
     *
     * @return int
     * @since 2.0
     */
    public function getPostIn($post, string $lang);


    /**
     * Get Taxonomy Term in desired $lang
     *
     * @param string $lang Language slug to get the term in
     * @param int    $t_id The term id
     *
     * @return int
     * @since 2.0
     */
    public function getTermIn(string $lang, string $t_id): int;


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
    public function getStringIn(string $string, string $domain, string $lang);


    /**
     * Get the post permalink in desired language.
     *
     * @param int|WP_Post|ARRAY_A  $post  The post
     * @param string               $lang  The desired language
     *
     * @return string|false
     * @since 2.0
     */
    public function getPostPermalinkIn($post, string $lang);
}
