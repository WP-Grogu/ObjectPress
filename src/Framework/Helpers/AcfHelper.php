<?php

namespace OP\Framework\Helpers;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.1
 * @access   public
 * @since    0.1
 */
class AcfHelper
{
    /**
     * Path to layouts thumbnails images
     * This path is relative from the current theme folder
     *
     * @access public
     * @var string
     * @since 0.1
     */
    public static $thumbnails_relative_folder_path = '/dist/images/blocks_thumbnails/';


    /**
     * Formats each blocs using AcfHelper::formatFields()
     *
     * @param array $blocks
     *
     * @return array of objects
     * @since 0.1
     */
    public static function formatBlocks($blocks)
    {
        foreach ($blocks as &$block) {
            $block = self::formatFields($block, $block['acf_fc_layout']);
        }

        return $blocks;
    }


    /**
     * Recursively format fields to remove layout prefix and convert to objects
     *
     * @param array  $fields
     * @param string $prefix
     *
     * @return object
     * @since 0.1
     */
    public static function formatFields($fields, $prefix = '')
    {
        $flds = [];

        foreach ($fields as $fld_name => $fld_value) {
            if ($fld_name === 'acf_fc_layout') {
                $name = '__layout';
                if (strpos($fld_value, 'bloc_') === 0) {
                    $fld_value = str_replace('bloc_', '', $fld_value);
                }
            } elseif (!empty($prefix) && strpos($fld_name, $prefix) === 0) {
                $name = str_replace($prefix . '_', '', $fld_name);
            } else {
                $name = $fld_name;
            }

            if (is_array($fld_value)) {
                if (is_string($fld_name)) {
                    if (strpos($fld_name, $prefix) !== false) {
                        $n_prefix = $fld_name;
                    } else {
                        $n_prefix = $prefix . '_' . $fld_name;
                    }
                }

                $fld_value = self::formatFields($fld_value, $n_prefix ?? $prefix);
            }

            $flds[$name] = $fld_value;
        }

        return (object) $flds;
    }


    /**
     * Register groups thumbnails
     * @since 0.1
     */
    public static function setThumbnails()
    {
        $acfjson = new AcfJsonHelper;

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
     * @since 0.1
     */
    private static function registerThumbnail($flexible_name, $layout_name)
    {
        add_filter("acfe/flexible/layout/thumbnail/name={$flexible_name}&layout={$layout_name}", function ($thumbnail, $field, $layout) {
            $theme_dir = str_replace('/resources', '', get_template_directory());

            $thumb_fld = $theme_dir . \App\Helpers\AcfHelper::$thumbnails_relative_folder_path;
            $uri       = '/app/themes/' . explode('/themes/', $thumb_fld)[1];

            $exts = [
                '.png',
                '.jpg',
                '.gif'
            ];

            $furl  = $uri . $layout['name']; // url to get from
            $fpath = $thumb_fld . $layout['name']; // real server path of the img

            foreach ($exts as $ext) {
                if (file_exists($fpath . $ext)) {
                    return $furl . $ext;
                }
            }

            return;
        }, 10, 3);
    }


    /**
     * Read fields and sort blocks into objects (for format: 'prefix_blockname_field_subfield)
     *
     * @param array
     * @param string Blocks fields prefix
     * @return object
     * @since 0.1
     */
    public static function fieldsToBlocks($fields, $prefix = 'bloc_')
    {
        $blocks = [];

        if (is_array($fields)) {
            foreach ($fields as $fld_name => $fld_value) {
                if (strpos($fld_name, $prefix) !== false) {
                    $arr = explode('_', $fld_name); // Get the bloc name without bloc_ prefix

                    if (count($arr) > 2) {
                        // Fields based (eg. 'bloc_intro_bgimage')
                        $blocks[$arr[1]][$fld_name] = $fld_value;
                    } elseif (count($arr) === 2) {
                        // Repeator based (eg. : institutionnal repetor)
                        $blocks[$arr[1]][] = $fld_value;
                    }
                }
            }
        }

        foreach ($blocks as $name => &$block) {
            // Format each blocks
            $block              = self::formatFields($block, $prefix . $name);
            $block->__layout    = $name;

            // Remove unecessary stuffs
            if (isset($block->acfe_flexible_layout_title)) {
                unset($block->acfe_flexible_layout_title);
            }
        }

        return $blocks;
    }


    /**
     * Sort the blocks given a sorted array with blocks keys (eg: ['intro', 'slidercircle'])
     * If a block has no sort-index, it will be pushed at the end of the other blocks
     *
     * @param array $blocks Blocks to sort
     * @param array $sort   Sorted array of blocks keys
     *
     * @return array
     * @since 0.1
     */
    public static function sortBlocks($blocks, $sort)
    {
        $sorted = [];

        foreach ($sort as $elem) {
            if (key_exists($elem, $blocks)) {
                $sorted[$elem] = $blocks[$elem];
                unset($blocks[$elem]);
            }
        }

        return $sorted + $blocks;
    }
}
