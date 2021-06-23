<?php

namespace OP\Support\Language\Drivers;

use OP\Framework\Contracts\LanguageDriver as LanguageDriverContract;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    2.0
 */
abstract class AbstractDriver implements LanguageDriverContract
{
    /**
     * Get all front page ids (in all translations)
     *
     * @return array
     * @since 2.0
     */
    public function getFrontPageIds()
    {
        $front_id = absint(get_option('page_on_front', 0));

        if (!$front_id) {
            return [];
        }

        return $this->getPostTranslations($front_id);
    }


    /**
     * Get all translation ids of a post given it's id/WP_Post/post.
     *
     * @param int|WP_Post|ARRAY_A  $post  The post
     *
     * @return array of ints (post ids)
     * @since 2.0
     */
    public function getPostTranslations($post)
    {
        $langs = $this->getAvailableLanguages();
        $posts = [];

        foreach ($langs as $lang) {
            $posts[$lang->slug] = $this->getPostIn($post, $lang->slug);
        }

        return $posts;
    }


    /**
     * Transfom given locale into slug.
     *
     * @return string
     */
    public function localeToSlug(string $locale): string
    {
        $matches = [];

        if (preg_match('/^[a-z]{2}$/', $locale)) {
            return $locale;
        }
        if (preg_match('/^([a-z]{2})_[A-Z]{2}$/', $locale, $matches)) {
            return $matches[1];
        }

        return '';
    }


    /**
     * Transfom given locales into slugs.
     *
     * @return array
     */
    public function localesToSlugs(array $locales): array
    {
        return collect($locales)->map([$this, 'localeToSlug'])->filter()->toArray();
    }
}
