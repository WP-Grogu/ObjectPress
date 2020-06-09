<?php

namespace OP\Framework\Models;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.3.1
 * @access   public
 * @since    0.1
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
}
