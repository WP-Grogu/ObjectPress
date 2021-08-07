<?php

namespace OP\Thirdparty\ACF;

use OP\Support\Facades\Theme;
use OP\Support\Facades\Config;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @since    1.0
 * @access   public
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
        Theme::on("acfe/flexible/thumbnail", function ($thumbnail, $field, $layout) {
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
