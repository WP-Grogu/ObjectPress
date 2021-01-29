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
     * Find pages with given template.
     * Template name can be full (eg: 'template-example.php') or simplified (eg: 'example').
     *
     * @param string $template  The template name.
     * @param bool   $unique    If set to tru, return only the first page found. Default to false.
     *
     * @return array
     */
    public static function getByTemplate(string $template, bool $unique = false)
    {
        $wp_pages = PostHelper::getTemplatePages($template, $unique ? 1 : 0);
        $results  = [];

        if (empty($wp_pages)) {
            return [];
        }

        if ($unique) {
            return static::find($wp_pages[0]);
        }

        foreach ($wp_pages as $p) {
            $results[] = static::find($p);
        }

        return $results;
    }
}
