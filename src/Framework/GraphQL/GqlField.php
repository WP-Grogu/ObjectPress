<?php

namespace OP\Framework\GraphQL;

class GqlField
{
    /**
     * Register a graphQL field on a given target
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
