<?php

namespace OP\Framework\Contracts;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  2.0
 * @access   public
 * @since    1.0.4
 */
interface ApiRoute
{
    public function resolve(object $args, object $body_args);
}
