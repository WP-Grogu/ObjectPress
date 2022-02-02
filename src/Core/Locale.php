<?php

namespace OP\Core;

use OP\Framework\Helpers\LanguageHelper;
use OP\Core\Patterns\SingletonPattern;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.2
 * @access   public
 * @since    1.0.1
 */
final class Locale
{
    use SingletonPattern;

    private static $paths = [];


    /**
     * Get a i18n item from config files in default language.
     * You can specify another language by using $lang param.
     * Returns false if key is not existing in selected lang.
     *
     * @param string $key
     * @param string $lang
     *
     * @return string
     */
    public function get(string $key, string $lang = '')
    {
        if (strpos($key, '.') === false) {
            return '';
        }

        $key    = explode('.', $key);
        $item   = array_pop($key);
        $domain = implode('.', $key);

        $items = $this->getDomain($domain, $lang);

        if (!$items || !array_key_exists($item, $items)) {
            return '';
        }

        return $items[$item];
    }


    /**
     * Get a Domain translation array in default language.
     * You can specify another language by using $lang param.
     * Returns false if domain is not existing in selected lang.
     *
     * @param string $domain
     * @param string $lang
     *
     * @return array|false
     */
    public function getDomain(string $domain, string $lang = '')
    {
        if ($lang === '') {
            $lang = static::defaultLang();
        }

        $relative_path = $this->domainToRelPath($domain, $lang);
        $full_path     = $this->relativeToFullPath($relative_path);

        // Fall back to english if lang not found
        if (!$full_path) {
            $relative_path = $this->domainToRelPath($domain, 'en');
            $full_path     = $this->relativeToFullPath($relative_path);
        }

        if (!$full_path) {
            return false;
        }

        return include $full_path;
    }


    /**
     * Iterate into paths to find the first presence of $relative_path.
     * If the relative path can't be found on any paths, returns false
     *
     * @param string $relative_path
     *
     * @return string|false
     */
    private function relativeToFullPath(string $relative_path)
    {
        foreach (static::$paths as $path) {
            $test_path = implode('', [$path, '/', $relative_path]);

            if (file_exists($test_path)) {
                return $test_path;
            }
        }

        return false;
    }


    /**
     * From domain, get relative file path
     *
     * @param string $domain
     * @param string $lang
     *
     * @return string
     */
    private function domainToRelPath(string $domain, string $lang): string
    {
        return implode('', [
            strtolower($lang),
            '/',
            str_replace('.', '/', strtolower($domain)),
            '.php'
        ]);
    }


    /**
     * Returns the default language.
     * It first reads App constant OP_DEFAULT_APP_LOCALE,
     * then lang wp-plugins data, and then WP defaults.
     *
     * @return string (lang slug)
     */
    public static function defaultLang()
    {
        if (defined('OP_DEFAULT_APP_LOCALE') && !is_null(OP_DEFAULT_APP_LOCALE)) {
            return OP_DEFAULT_APP_LOCALE;
        }

        $lang = LanguageHelper::getPrimaryLang();

        if ($lang) {
            return $lang;
        }

        $lang = get_locale();

        if (strpos($lang, '_') !== false) {
            $lang = explode('_', $lang)[0];
        }

        return $lang;
    }


    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
        static::$paths[] = realpath(__DIR__ . '/../Lang/');
    }
}
