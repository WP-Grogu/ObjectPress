<?php

namespace OP\Thirdparty\ACF;

use OP\Support\Facades\Theme;
use OP\Support\Facades\Config;
use OP\Thirdparty\ACF\Classes\JsonReader;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.0
 */
class AcfManager
{
    /**
     * Register flexible content layouts thumbnails reading acf JSON files
     *
     * @return void
     * @since 1.0.0
     * @version 2.0
     */
    public static function setThumbnails()
    {
        $acfjson = new JsonReader;

        foreach ($acfjson->getGroups() as $group) {
            foreach ($group->fields as $field) {
                if (isset($field->type) && $field->type === 'flexible_content') {
                    foreach ($field->layouts as $layout) {
                        self::registerThumbnail($field->name, $layout->name);
                    }
                }
            }
        }
    }


    /**
     * Register a filter for a given layout
     *
     * @param string $flexible_name Name of the flexible content field
     * @param string $layout_name Name of the layout
     *
     * @return void
     * @since 1.0.0
     * @version 2.0
     */
    private static function registerThumbnail(string $flexible_name, string $layout_name): void
    {
        Theme::on("acfe/flexible/thumbnail/name={$flexible_name}&layout={$layout_name}", function ($thumbnail, $field, $layout) {
            $rel_paths = array_filter(Config::get('object-press.acf.flex-thumb-relative-path'));

            if (empty($rel_paths)) {
                return;
            }

            $exts = [
                '.png',
                '.jpg',
                '.jpeg',
                '.gif',
            ];

            foreach ($rel_paths as $rel_path) {
                $rel_path = trim($rel_path, '/');
                $furl     = sprintf('%s/%s/%s', \get_template_directory_uri(), $rel_path, $layout['name']);  // url to get from
                $fpath    = sprintf('%s/%s/%s', \get_template_directory(), $rel_path, $layout['name']);      // real server path of the img
    
                foreach ($exts as $ext) {
                    if (file_exists($fpath . $ext)) {
                        return $furl . $ext;
                    }
                }
            }

            return;
        }, 10, 3);
    }
}
