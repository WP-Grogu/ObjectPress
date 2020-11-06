<?php

namespace OP\Framework\GraphQL;

use OP\Framework\GraphQL\Interfaces\IGqlField;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.0.4
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
            'resolve'       => [static::class, 'resolve____op']
        ];

        foreach (static::$targets as $target) {
            register_graphql_field($target, static::$field_name, $field_props);
        }
    }

    /**
     * Init method for app autoload
     */
    public static function init()
    {
        static::register();
    }


    /**
     * GraphQL resolve callback
     *
     * @param \WP_Post $post
     *
     * @return string
     * @since 1.0.4
     */
    public static function resolve____op($post, $args, $context, $info)
    {
        do_action('op_graphql_field_resolve_before', $post, $args, $context, $info);

        $r = static::resolve($post, $args, $context, $info);

        do_action('op_graphql_field_resolve_after', $post, $args, $context, $info);

        return $r;
    }
}
