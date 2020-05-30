<?php

namespace OP\Framework\GraphQL\Interfaces;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.3
 * @access   public
 * @since    1.0.3
 */
interface IGqlField
{
    public static function register();
    public static function resolve($post, $args, $context, $info);
}
