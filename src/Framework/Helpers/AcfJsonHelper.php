<?php

namespace OP\Framework\Helpers;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.1
 * @access   public
 * @since    0.1
 */
class AcfJsonHelper
{
    /**
     * ACF json files folder
     *
     * @var string
     * @access private
     * @since 0.1
     */
    private $acf_path;


    /**
     * Class constructor
     *
     * @since 0.1
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
     * @since 0.1
     */
    public function getGroups(): array
    {
        $files = $this->getAcfJsonFileNames();
        $fld_groups = [];

        if (!empty($files)) {
            foreach ($files as $file) {
                $fld_groups[] = $this->jsonToFields($file);
            }
        }

        return $fld_groups;
    }


    /**
     * Retrieve JSON files
     *
     * @return array
     * @since 0.1
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
     * @since 0.1
     */
    private function jsonToFields($file)
    {
        return json_decode(file_get_contents($this->acf_path . '/' . $file));
    }
}
