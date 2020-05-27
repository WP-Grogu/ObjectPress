<?php

namespace OP\Framework\GraphQL;

use OP\Framework\GraphQL\Interfaces\IGqlType;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.3
 * @access   public
 * @since    1.3
 */
abstract class GqlType implements IGqlType
{
    /**
     * Register a graphQL type on a given target
     * @return void
     */
    public static function register()
    {
        foreach (static::$fields as &$field) {
            $field['description'] = __($field['description'], 'wp-graphql');
        }

        $type_props = [
            'description'   => __(static::$type_description, 'wp-graphql'),
            'fields'        => static::$fields,
        ];

        register_graphql_object_type(static::$type_name, $type_props);
    }
}
