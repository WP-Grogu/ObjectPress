<?php

namespace OP\Framework\Models;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.5
 * @access   public
 * @since    1.0.3
 */
class Post extends PostModel
{
    /**
     * Wordpress post_type associated to the current model
     */
    public static $post_type = 'post';
}
