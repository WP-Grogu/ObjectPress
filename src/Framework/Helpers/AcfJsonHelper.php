<?php

namespace OP\Framework\Helpers;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.0
 * @access   public
 * @since    1.0.0
 */
class AcfJsonHelper
{
    /**
     * ACF json files folder
     *
     * @var string
     * @access private
     * @since 1.0.0
     */
    private $acf_path;


    /**
     * Class constructor
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->acf_path = WP_CONTENT_DIR . '/mu-plugins/148-tools/ACF/acf-json';

        if (!file_exists($this->acf_path)) {
            return false;
        }
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
                $group_name = substr($file, 0, strrpos($file, '.'));
                $fld_groups[$group_name] = $this->jsonToFields($file);
            }
        }

        return $fld_groups;
    }


    /**
     * Retrieve JSON files
     *
     * @return array
     * @since 1.0.0
     */
    private function getAcfJsonFileNames(): array
    {
        $files = scandir($this->acf_path);

        if (!$files) {
            return [];
        }

        return array_filter($files, function ($file) {
            return strpos($file, '.json') !== false;
        }) ?? [];
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
        return json_decode(file_get_contents($this->acf_path . '/' . $file));
    }
}
