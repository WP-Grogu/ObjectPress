<?php

namespace OP\Framework\GraphQL;

use OP\Framework\GraphQL\Interfaces\IGqlField;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.3
 * @access   public
 * @since    1.0.3
 */
abstract class GqlField implements IGqlField
{
    /**
     * Register a GraphQL field on given target(s)
     *
     * @return void
     */
    public static function register()
    {
        $field_props = [
            'type'          => static::$field_type,
            'description'   => __(static::$field_description, 'wp-graphql'),
            'resolve'       => [static::class, 'resolve']
        ];

        foreach (static::$targets as $target) {
            register_graphql_field($target, static::$field_name, $field_props);
        }
    }
}
