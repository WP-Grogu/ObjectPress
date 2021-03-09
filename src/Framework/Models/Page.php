<?php

namespace OP\Framework\Models;

use OP\Framework\Helpers\PostHelper;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.3
 */
class Page extends Post
{
    /**
     * Wordpress post_type associated to the current model
     */
    public static $post_type = 'page';


    /**
     * Return page's associated template slug
     *
     * @param bool $full_path Should retreive full template filepath or slug only
     *
     * @return string
     */
    public function getTemplate($full_path = false)
    {
        $func = $full_path ? 'get_page_template' : 'get_page_template_slug';

        return $func($this->id);
    }


    /**
     * ⚠️ deprecated, please use whereTemplate() instead.
     *
     * Find page(s) with specified template.
     * Template name can be full (eg: 'template-example.php') or simplified (eg: 'example').
     *
     * @param string $template  The template name.
     * @param bool   $unique    If set to true, return only the first page found. Default to false.
     *
     * @return array
     * @since 1.0.4
     * @deprecated 1.0.5
     */
    public static function getByTemplate(string $template, bool $unique = false)
    {
        return static::whereTemplate($template, $unique);
    }


    /**
     * Find page(s) with specified template.
     * Template name can be full (eg: 'template-home.php') or simplified (eg: 'home').
     *
     * ℹ️ You can change your theme templates filenames structure in config, cf. `object-press.wp.template-files-structure`
     *
     * @param string $template  The template name.
     * @param bool   $unique    If set to true, only return the first page found. Default: false.
     *
     * @return Page|array|null If $unique set to false, return a collection of pages
     * @since 1.0.5
     */
    public static function whereTemplate(string $template, bool $unique = false)
    {
        $wp_pages = PostHelper::getTemplatePages($template, $unique ? 1 : 0);

        if (empty($wp_pages)) {
            return $unique ? null : [];
        }

        return $unique ? static::find($wp_pages[0]) : static::collection($wp_pages);
    }
}
