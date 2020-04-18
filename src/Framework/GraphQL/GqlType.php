<?php

namespace OP\Framework\GraphQL;

class GqlType
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
