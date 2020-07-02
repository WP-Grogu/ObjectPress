<?php

namespace OP\Core;

use OP\Core\Patterns\SingletonPattern;

final class Config
{
    use SingletonPattern;

    private static $paths = [];

    /**
     * Get a single config item from it's full key path
     * eg. Config/auth.php => auth.encryption
     *
     * @param string $key
     *
     * @return string|false on failure
     */
    public function get(string $key)
    {
        if (strpos($key, '.') === false) {
            return '';
        }

        $key    = explode('.', $key);
        $item   = array_pop($key);
        $domain = implode('.', $key);

        $items = $this->getDomain($domain);

        if (!$items || !array_key_exists($item, $items)) {
            return false;
        }

        return $items[$item];
    }

    /**
     * Get a config Domain array from it's path
     *
     * @param string $domain
     *
     * @return array|false
     */
    public function getDomain(string $domain)
    {
        $relative_path = $this->domainToRelPath($domain);
        $full_path     = $this->relativeToFullPath($relative_path);

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
     *
     * @return string
     */
    private function domainToRelPath(string $domain): string
    {
        return implode('', [
            str_replace('.', '/', strtolower($domain)),
            '.php'
        ]);
    }


    /**
     * Add a path to list of paths
     *
     * @param  string|array $paths
     * @return void
     */
    public function addPath($paths)
    {
        if (is_string($paths)) {
            $paths = [$paths];
        }

        if (!is_array($paths)) {
            throw new \Exception("OP : Error : Adding a path to config class must be a string or an array");
        }

        array_map('realpath', $paths);

        array_unshift(static::$paths, ...$paths);
    }


    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
        $_theme = get_template_directory() . '/config';
        $_base  = __DIR__ . '/../Config/';

        $this->addPath([$_theme, $_base]);
    }
}
