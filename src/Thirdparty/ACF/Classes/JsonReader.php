<?php

namespace OP\Thirdparty\ACF\Classes;

use OP\Support\Facades\Config;
use OP\Framework\Exceptions\FileNotFoundException;
use Illuminate\Support\Arr;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    1.0.0
 */
class JsonReader
{
    /**
     * ACF json files folder
     *
     * @var array
     * @access private
     * @since 1.0.0
     */
    private $acf_paths;


    /**
     * Class constructor
     *
     * @since 1.0.0
     * @version 1.0.5
     */
    public function __construct()
    {
        $paths = Config::get('object-press.acf.json-path');

        if (!is_array($paths)) {
            $paths = [$paths];
        }

        $paths = array_filter(array_map(function ($p) {
            return (is_string($p) && file_exists($p) && is_dir($p)) ? $p : false;
        }, $paths));

        if (empty($paths)) {
            throw new FileNotFoundException(
                "ObjectPress: Could not load the acf folders, perhaps your configuration is invalid. Please make sure the `object-press.acf.json-path` conf is right"
            );
        }

        $this->acf_paths = $paths;
    }


    /**
     * Get acf fields groups from JSON config files
     * If there is no files to checkup, will return an empty array
     *
     * @return array of objects
     * @since 1.0.0
     */
    public function getGroups(): array
    {
        $files = $this->getAcfJsonFileNames();
        $fld_groups = [];

        if (!empty($files)) {
            foreach ($files as $file) {
                $file_name   = array_slice(explode('/', $file), -1)[0];
                $group_name  = substr($file_name, 0, strrpos($file_name, '.'));

                $fld_groups[$group_name] = $this->jsonToFields($file);
            }
        }

        return $fld_groups;
    }


    /**
     * Retrieve JSON files
     *
     * @return array
     * @version 2.0
     * @since 1.0.0
     */
    private function getAcfJsonFileNames(): array
    {
        $files = [];

        foreach ($this->acf_paths as $path) {
            $dir_content = scandir($path);

            $dir_content = array_filter($dir_content, function ($file) {
                return strpos($file, '.json') !== false;
            });

            $dir_content = array_map(function ($f) use ($path) {
                return sprintf('%s/%s', $path, $f);
            }, $dir_content);

            $files = array_merge($files, $dir_content);
        }

        return $files;
    }


    /**
     * Read a single JSON file and get required fields informations
     *
     * @param string $file Json file name
     * @return array
     * @since 1.0.0
     */
    private function jsonToFields($file)
    {
        return json_decode(file_get_contents($file));
    }
}
