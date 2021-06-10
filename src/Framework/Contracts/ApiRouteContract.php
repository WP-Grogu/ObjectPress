<?php

namespace OP\Framework\Contracts;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.4
 * @access   public
 * @since    1.0.4
 */
interface ApiRouteContract
{
    public static function resolve(object $args, object $body_args);
}
